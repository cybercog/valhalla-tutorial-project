<?php

namespace App\Http\Middleware;

use App\Application;
use Closure;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Validator;

class ApiAppAuth
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
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

            $app = Application::whereKey($payload['sub'])->firstOrFail();
        } catch (\Firebase\JWT\ExpiredException $e) {
            return response('token_expired', 401);
        } catch (\Throwable $e) {
            return response('token_invalid', 401);
        }

        if (! $app->is_active) {
            return response('app_inactive', 403);
        }

        $request->merge(['__authenticatedApp' => $app]);

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
            'iss' => 'required|in:valhalla',
            'sub' => 'required',
        ]);

        if (! $validator->passes()) {
            throw new \InvalidArgumentException;
        }
    }
}
