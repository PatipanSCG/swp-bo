<?php

namespace App\Http\Controllers;

use App\Services\WamAuth;
class WamAuthTestController extends Controller
{
    public function index(WamAuth $wamAuth)
    {
         $token = $wamAuth->login();

        if ($token) {
            return response()->json([
                'accessToken' => $token
            ]);
        }

        return response()->json([
            'error' => 'Login failed'
        ], 401);
    }
}
