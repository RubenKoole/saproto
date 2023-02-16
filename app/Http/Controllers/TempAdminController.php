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
    private const ERROR_CONTACTING_PROTUBE = "Couldn't contact Protube";
    private const ERROR_REMOVING_ADMINISTRATOR = "Couldn't remove administrator: ";
    const ERROR_ADDING_ADMINISTRATOR = "Couldn't add administrator: ";

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
     * Removes protube admin rights from the user of the given ID
     * @param int $id
     * @return RedirectResponse
     */
    public function end($id)
    {
        /** @var User $user */
        $user = User::findOrFail($id);

        foreach ($user->tempadmin as $tempadmin) {
            if (Carbon::now()->between(Carbon::parse($tempadmin->start_at), Carbon::parse($tempadmin->end_at))) {
                $this->removeAdmin($tempadmin);
            }
        }
        return Redirect::back();
    }

    /**
     * Removes protube admin rights from the tempadmin user of the given ID
     * @param int $id
     * @return RedirectResponse
     * @throws Exception
     */
    public function endId($id)
    {
        /** @var Tempadmin $tempadmin */
        $tempadmin = Tempadmin::findOrFail($id);
        $this->removeAdmin($tempadmin);
        return Redirect::back();
    }

    /**
     * Removes the tempadmin from the db and from protube.
     * Shows a session flash when an error occurred
     * @param $tempAdmin Tempadmin The temp admin to be removed
     * @return void
     */
    private function removeAdmin($tempAdmin) {
        // Only send the request to remove the admin rights if the user has become an admin
        if (!Carbon::parse($tempAdmin->start_at)->isFuture())  {
            // sends a request to protube to remove the user
            $response = $this->changeProtubeAdmin($tempAdmin->user_id, false);
            if ($response->ok()) {
                $json = $response->json();
                if (!$json['success']) Session::flash('flash_message', self::ERROR_REMOVING_ADMINISTRATOR . $json['message']);
            } else {
                Session::flash('flash_message', self::ERROR_CONTACTING_PROTUBE);
            }
        }
        // removes the temp admin from the db
        $tempAdmin->delete();
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

        // Request protube to add the admin
        $response = $this->changeProtubeAdmin($tempadmin->user_id, true);
        if ($response->ok()) {
            $json = $response->json();

            if ($json['success']) {
                $tempadmin->save();
                return Redirect::route('tempadmin::index');
            }
            else Session::flash('flash_message', self::ERROR_ADDING_ADMINISTRATOR . $json['message']);
        } else {
            Session::flash('flash_message', self::ERROR_CONTACTING_PROTUBE);
        }
        return Redirect::back();
    }

    /**
     * @param int $userID the id of the user that should be updated
     * @param bool $becomeAdmin if the user should become an admin
     * @return PromiseInterface|Response the response from the http post request
     */
    private function changeProtubeAdmin(int $userID, bool $becomeAdmin) {
        return Http::withHeaders([
            'Authorization' => sprintf('Bearer %d', config('protube.secret')),
            'Content-Type' => 'application/json',
        ])->withOptions(["verify"=>false])->post(config('protube.server'), [
            'user_id' => $userID,
            'admin' => $becomeAdmin ? '1' : '0'
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
