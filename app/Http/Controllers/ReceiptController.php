<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    public function download(int $id) {
        $payment = Payment::with('user')->findOrFail($id)->toArray();
        
        $pdf = Pdf::loadView('user.financial.receipt', ['payment' => $payment]);
        $pdf->setOption('orientation', 'landscape');
        return $pdf->download($payment['receipt_id'] . '.pdf');
    }
}
