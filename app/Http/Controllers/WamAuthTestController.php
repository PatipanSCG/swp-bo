<?php

namespace App\Http\Controllers;

use App\Services\WamAuth;
class WamAuthTestController extends Controller
{
    public function index(WamAuth $wamAuth)
    {
         $token = $wamAuth->login();
         $data = $wamAuth->getVerifyRequests($token, [
            'page' => 1,
            'limit' => 20,
        ]);

        return response()->json($data['data']);

    }
}
