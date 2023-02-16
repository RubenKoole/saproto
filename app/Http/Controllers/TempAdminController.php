<?php

namespace Proto\Http\Controllers;

use Auth;
use Carbon;
use DB;
use Exception;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;
use Proto\Models\Tempadmin;
use Proto\Models\User;
use Redirect;
use Session;

class TempAdminController extends Controller
{
    /**
     * @param int $id
     * @return RedirectResponse
     */
    public function make($id)
    {
        $user = User::findOrFail($id);

        $tempAdmin = new Tempadmin();
        $tempAdmin->created_by = Auth::user()->id;
        $tempAdmin->start_at = Carbon::today();
        $tempAdmin->end_at = Carbon::tomorrow();
        $tempAdmin->user()->associate($user);
        $tempAdmin->save();

        return Redirect::back();
    }

    /**
     * @param int $id
     * @return RedirectResponse
     */
    public function end($id)
    {
        /** @var User $user */
        $user = User::findOrFail($id);

        foreach ($user->tempadmin as $tempadmin) {
            if (Carbon::now()->between(Carbon::parse($tempadmin->start_at), Carbon::parse($tempadmin->end_at))) {
                $tempadmin->end_at = Carbon::now()->subSeconds(1);
                $tempadmin->save();
            }
        }

        // Call Herbert webhook to run check through all connected admins.
        // Will result in kick for users whose temporary admin powers were removed.

        //disabled because protube is down/it is not implemented in the new one yet
        //Http::get(config('herbert.server').'/adminCheck');

        return Redirect::back();
    }

    /**
     * @param int $id
     * @return RedirectResponse
     * @throws Exception
     */
    public function endId($id)
    {
        /** @var Tempadmin $tempadmin */
        $tempadmin = Tempadmin::findOrFail($id);

        if (Carbon::parse($tempadmin->start_at)->isFuture()) {
            $tempadmin->delete();
        } else {
            $tempadmin->end_at = Carbon::now()->subSeconds(1);
            $tempadmin->save();

            // Call Herbert webhook to run check through all connected admins.
            // Will result in kick for users whose temporary admin powers were removed.

            //disabled because protube is down/it is not implemented in the new one yet
            //Http::get(config('herbert.server').'/adminCheck');
        }

        return Redirect::back();
    }

    /**
     * @return View
     */
    public function index()
    {
        $tempadmins = Tempadmin::where('end_at', '>', DB::raw('NOW()'))->orderBy('end_at', 'desc')->get();
        $pastTempadmins = Tempadmin::where('end_at', '<=', DB::raw('NOW()'))->orderBy('end_at', 'desc')->take(10)->get();

        return view('tempadmin.list', ['tempadmins' => $tempadmins, 'pastTempadmins' => $pastTempadmins]);
    }

    /** @return View */
    public function create()
    {
        return view('tempadmin.edit', ['tempadmin' => null, 'new' => true]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $tempadmin = new Tempadmin();
        $tempadmin->user()->associate(User::findOrFail($request->user_id));
        $tempadmin->creator()->associate(Auth::user());
        $tempadmin->start_at = date('Y-m-d H:i:s', strtotime($request->start_at));
        $tempadmin->end_at = date('Y-m-d H:i:s', strtotime($request->end_at));
        $tempadmin->save();


        $response = $this->changeProtubeAdmin($tempadmin->user_id, true);
        if ($response->ok()) {
            $json = $response->json();
            if ($json['success']) return Redirect::route('tempadmin::index');
            else Session::flash('flash_message', sprintf("%s %d", $json['message'], $tempadmin->user_id));
        } else {
            Session::flash('flash_message', "Couldn't contact Protube");
        }
        return Redirect::back();
    }

    /**
     * @param string $userID the id of the user that should be updated
     * @param bool $becomeAdmin if the user should become an admin
     * @return PromiseInterface|Response the response from the http post request
     */
    private function changeProtubeAdmin(int $userID, bool $becomeAdmin) {
        return Http::withHeaders([
            'Authorization' => sprintf('Bearer %d', config('protube.secret')),
            'Content-Type' => 'application/json',
        ])->withOptions(["verify"=>false])->post(config('protube.server'), [
            'user_id' => $userID,
            'admin' => $becomeAdmin
        ]);
}

    /**
     * @param int $id
     * @return View
     */
    public function edit($id)
    {
        $tempadmin = Tempadmin::findOrFail($id);
        return view('tempadmin.edit', ['item' => $tempadmin, 'new' => false]);
    }

    /**
     * @param int $id
     * @param Request $request
     * @return RedirectResponse
     */
    public function update($id, Request $request)
    {
        /** @var Tempadmin $tempadmin */
        $tempadmin = Tempadmin::findOrFail($id);
        $tempadmin->start_at = date('Y-m-d H:i:s', strtotime($request->start_at));
        $tempadmin->end_at = date('Y-m-d H:i:s', strtotime($request->end_at));
        $tempadmin->save();

        return Redirect::route('tempadmin::index');
    }
}
