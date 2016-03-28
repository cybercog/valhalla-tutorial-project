<?php

namespace App\Http\Controllers\Api;

use App\Application;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Get the authenticated application data.
     *
     * @param Request $request
     */
    public function appData(Request $request)
    {
        return $request->__authenticatedApp;
    }

    /**
     * Get the authenticated user data.
     *
     * @param Request $request
     */
    public function userData(Request $request)
    {
        return [
            'app' => $request->__authenticatedApp,
            'user' => $request->__authenticatedUser,
        ];
    }
}
