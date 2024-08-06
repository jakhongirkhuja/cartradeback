<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Services\Payment\PaymeService;
use Illuminate\Http\Request;

class PaymeController extends Controller
{
    public function index(Request $request, PaymeService $paymeService){
        return $paymeService->index($request);
    }
}
