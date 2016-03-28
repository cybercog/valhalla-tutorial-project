<?php

namespace App\Http\Middleware;

use Firebase\JWT\JWT;
use App\Application;
use App\User;
use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ApiUserAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $authToken = $request->bearerToken();

        try {
            $this->payloadIsValid(
                $payload = (array) JWT::decode($authToken, 'w5yuCV2mQDVTGmn3', ['HS256'])
            );

            $app = Application::whereKey($payload['iss'])->firstOrFail();

            $user = User::whereEmail($payload['sub'])->firstOrFail();
        } catch (\Throwable $e) {
            return response('token_invalid', 401);
        }

        if (! $app->is_active) {
            return response('app_inactive', 403);
        }

        if (DB::table('tokens_cemetery')->whereTokenId($payload['jti'])->first()) {
            return response('token_deceased', 403);
        }

        $request->merge(['__authenticatedApp' => $app]);

        $request->merge(['__authenticatedUser' => $user]);

        $request->merge(['__authTokenId' => $payload['jti']]);

        return $next($request);
    }

    /**
     * Validate the JSON Web Token claims.
     *
     * @param $payload
     */
    private function payloadIsValid($payload)
    {
        $validator = Validator::make($payload, [
            'iss' => 'required',
            'sub' => 'required',
            'jti' => 'required',
        ]);

        if (! $validator->passes()) {
            throw new \InvalidArgumentException;
        }
    }
}
