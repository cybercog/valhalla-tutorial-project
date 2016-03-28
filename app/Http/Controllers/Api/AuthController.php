<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Application;

class AuthController extends Controller
{
    /**
     * Authenticate a third-party application.
     *
     * @param Request $request
     */
    public function authenticateApp(Request $request)
    {
        $credentials = base64_decode(
            Str::substr($request->header('Authorization'), 6)
        );

        try {
            list($appKey, $appSecret) = explode(':', $credentials);

            $app = Application::whereKeyAndSecret($appKey, $appSecret)->firstOrFail();
        } catch (\Throwable $e) {
            return response('invalid_credentials', 400);
        }

        if (! $app->is_active) {
            return response('app_inactive', 403);
        }

        return response([
            'token_type' => 'Bearer',
            'access_token' => $app->generateAuthToken(),
        ]);
    }

    /**
     * Authenticate a user from a third-party application.
     *
     * @param Request $request
     */
    public function authenticateUser(Request $request)
    {
        $code = $request->json('code');

        $app = $request->__authenticatedApp;

        if (! $code || ! $user = $app->users()->wherePivot('authorization_code', $code)->first()) {
            return response('invalid_code', 400);
        }

        $app->users()->updateExistingPivot($user->id, ['authorization_code' => null]);

        return response([
            'token_type' => 'Bearer',
            'access_token' => $user->generateAuthToken($app),
        ]);
    }

    /**
     * Log a user out from a third-party application.
     *
     * @param Request $request
     */
    public function logoutUser(Request $request)
    {
        $token = $request->bearerToken();

        DB::table('tokens_cemetery')->insert(['token_id' => $request->__authTokenId]);

        return response('token_deceased');
    }
}
