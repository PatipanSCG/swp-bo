<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NavCustomer;

class NavCustomerController extends Controller
{
    public function index($vatid)
      {
        $customers = NavCustomer::whereRaw("[VAT Registration No_] = ?", [$vatid])->get();

        $data = json_decode(json_encode($customers, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE), true);

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
