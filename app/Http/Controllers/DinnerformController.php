<?php

namespace Proto\Http\Controllers;
use Auth;
use Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Proto\Models\Dinnerform;
use Proto\Models\DinnerformOrderline;
use Proto\Models\Product;
use Session;

class DinnerformController extends Controller
{
    /**
     * @param  int $id
     * @return View|RedirectResponse
     */
    public function show($id)
    {
        /** @var Dinnerform $dinnerform */
        $dinnerform = Dinnerform::findOrFail($id);
        $order = DinnerformOrderline::query()
            ->where('user_id', Auth::user()->id)
            ->where('dinnerform_id', $dinnerform->id)
            ->first();
        if (! $dinnerform->isCurrent() && ! isset($order)) {
            Session::flash('flash_message', 'This dinnerform is closed and you have not ordered anything.');
            return Redirect::back();
        }
        return view('dinnerform.show', ['dinnerform'=>$dinnerform, 'order'=>$order]);
    }

    /** @return View */
    public function admin($id)
    {
        $dinnerform = Dinnerform::findOrFail($id);
        return view('dinnerform.admin', ['dinnerform'=>$dinnerform, 'orderList'=>$dinnerform->orderlines()->get()]);
    }

    /** @return View */
    public function create()
    {
        $dinnerformList = Dinnerform::all()->sortByDesc('end');
        return view('dinnerform.list', ['dinnerformCurrent' => null, 'dinnerformList' => $dinnerformList]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        if ($request->input('end') < $request->input('start')) {
            Session::flash('flash_message', 'You cannot let the dinnerform close before it opens.');
            return Redirect::back();
        }

        $dinnerform = Dinnerform::create([
            'restaurant' => $request->input('restaurant'),
            'description' => $request->input('description'),
            'url' => $request->input('url'),
            'start' => strtotime($request->input('start')),
            'end' => strtotime($request->input('end')),
            'helper_discount'=> $request->input('helper_discount'),
            'regular_discount'=> (100 - $request->input('regular_discount')) / 100,
            'event_id'=> $request->input('event_select') != '' ? $request->input('event_select') : null,
            'visible_home_page'=> $request->has('homepage'),
        ]);

        Session::flash('flash_message', "Your dinnerform at '".$dinnerform->restaurant."' has been added.");
        return Redirect::route('dinnerform::add');
    }

    /**
     * @param  int $id
     * @return View|RedirectResponse
     */
    public function edit($id)
    {
        $dinnerformCurrent = Dinnerform::findOrFail($id);
        if ($dinnerformCurrent->closed) {
            Session::flash('flash_message', 'You cannot update a closed dinnerform!');
            return Redirect::back();
        }
        $dinnerformList = Dinnerform::all()->sortByDesc('end');
        return view('dinnerform.list', ['dinnerformCurrent' => $dinnerformCurrent, 'dinnerformList' => $dinnerformList]);
    }

    /**
     * @param Request $request
     * @param  int $id
     * @return RedirectResponse|View
     */
    public function update(Request $request, $id)
    {

        if ($request->input('end') < $request->input('start')) {
            Session::flash('flash_message', 'You cannot let the dinnerform close before it opens.');
            return Redirect::back();
        }

        /** @var Dinnerform $dinnerform */
        $dinnerform = Dinnerform::findOrFail($id);

        if ($dinnerform->closed) {
            Session::flash('flash_message', 'You cannot update a closed dinnerform!');
            return Redirect::back();
        }

        $changed_important_details =
            $dinnerform->start->timestamp != strtotime($request->input('start')) ||
            $dinnerform->end->timestamp != strtotime($request->input('end')) ||
            $dinnerform->restaurant != $request->input('restaurant');

        $dinnerform->update([
            'restaurant' => $request->input('restaurant'),
            'description' => $request->input('description'),
            'url' => $request->input('url'),
            'start' => strtotime($request->input('start')),
            'end' => strtotime($request->input('end')),
            'helper_discount'=> $request->input('helper_discount'),
            'regular_discount'=> (100 - $request->input('regular_discount')) / 100,
            'event_id'=> $request->input('event_select') != '' ? $request->input('event_select') : null,
            'visible_home_page'=> $request->has('homepage'),
        ]);

        if ($changed_important_details) {
            Session::flash('flash_message', "Your dinnerform for '".$dinnerform->restaurant."' has been saved. You updated some important information. Don't forget to notify your participants with this info!");
        } else {
            Session::flash('flash_message', "Your dinnerform for '".$dinnerform->restaurant."' has been saved.");
        }

        return $this->edit($id);
    }

    /**
     * @param int $id
     * @return RedirectResponse
     * @throws Exception
     */
    public function destroy($id)
    {
        $dinnerform = Dinnerform::findOrFail($id);
        if(! $dinnerform->closed) {
            Session::flash('flash_message', "The dinnerform for '".$dinnerform->restaurant."' has been deleted.");
            $dinnerform->delete();
        } else {
            Session::flash('flash_message', 'The dinnerform is already closed and can not be deleted!');
        }
        return Redirect::route('dinnerform::add');
    }

    /**
     * Close the dinnerform by changing the end time to the current time.
     *
     * @param int $id
     * @return View
     */
    public function close($id)
    {
        /** @var Dinnerform $dinnerform */
        $dinnerform = Dinnerform::findOrFail($id);
        $dinnerform->end = Carbon::now();
        $dinnerform->save();
        return view('dinnerform.admin', ['dinnerform'=>$dinnerform, 'orderList'=>$dinnerform->orderlines()->get()]);
    }

    public function process($id)
    {
        $dinnerform = Dinnerform::findOrFail($id);
        $dinnerformOrderlines = $dinnerform->orderlines()->get();
        $product = Product::findOrFail(config('omnomcom.dinnerform-product'));

        foreach($dinnerformOrderlines as $dinnerformOrderline) {
            $product->buyForUser(
                $dinnerformOrderline->user,
                1,
                $dinnerformOrderline->price_with_discount,
                null,
                null,
                sprintf("Dinnerform from %s, ordered at $dinnerform->restaurant", date('d-m-Y', strtotime($dinnerform->end))),
                sprintf('dinnerform_orderline_processed_by_%u', Auth::user()->id)
            );
            $dinnerformOrderline->closed = true;
            $dinnerformOrderline->save();
        }
        $dinnerform->closed = true;
        $dinnerform->save();
        return Redirect::route('dinnerform::add')->with('flash_message', "All orderlines of $dinnerform->restaurant have been processed!");
    }
}
