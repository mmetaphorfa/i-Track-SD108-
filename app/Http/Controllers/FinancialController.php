<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class FinancialController extends Controller
{
    /* ---------------------------------------
        Financial management page
    --------------------------------------- */
    public function index() {
        return view('user.financial.index');
    }

    /* ---------------------------------------
        Financial management page
    --------------------------------------- */
    public function show(string $id) {
        $payment = Payment::with('user')->where('invoice_id', $id)->firstOrFail();
        
        return view('user.financial.show', compact('payment'));
    }
}
