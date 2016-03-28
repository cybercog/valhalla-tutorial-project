<?php

namespace App\Http\Controllers;

use App\Application;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    /**
     * Show a form for the user to authorise a 3rd party app.
     *
     * @param Request $request
     */
    public function showAuthorisationForm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'app_key' => 'required|exists:applications,key,is_active,1',
            'redirect_uri' => 'required:active_url',
        ]);

        if (! $validator->passes()) {
            return view('authorise-app')->withInvalid('true');
        }

        $app = Application::whereKey($request->app_key)->first();

        return view('authorise-app', compact('app'));
    }

    /**
     * Authorise an application to communicate on behalf of user.
     *
     * @param Request $request
     */
    public function authoriseApp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'app_key' => 'required|exists:applications,key,is_active,1',
            'redirect_uri' => 'required:active_url',
        ]);

        if (! $validator->passes()) {
            return redirect()->back()->withMessage('Invalid authorisation parameters.');
        }

        if (! Auth::validate($request->only(['email', 'password']))) {
            return redirect()->back()->withMessage('Wrong credentials.');
        }

        $app = Application::whereKey($request->app_key)->first();

        $user = User::whereEmail($request->email)->first();

        $pivotData = ['authorisation_code' => $code = sha1($app->id.':'.$user->id.str_random())];

        if ($app->users->contains($user)) {
            $app->users()->updateExistingPivot($user->id, $pivotData);
        } else {
            $app->users()->attach($user->id, $pivotData);
        }

        return redirect()->away($request->redirect_uri.'?code='.$code);
    }
}
