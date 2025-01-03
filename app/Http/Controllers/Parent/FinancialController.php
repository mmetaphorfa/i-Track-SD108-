<?php

namespace App\Http\Controllers\Parent;

use App\Models\Payment;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class FinancialController extends Controller
{
    /* ---------------------------------------
        Financial management page
    --------------------------------------- */
    public function index() {
        $payments = Payment::where('user_id', Auth::user()->id)->where('status', 'paid')->get();
        
        return view('parent.financial.index', compact('payments'));
    }

    /* ---------------------------------------
        Make payment page
    --------------------------------------- */
    public function create() {
        $user = Auth::user();

        // Create invoice for new parent
        do {
            $invoiceId = $this->generateInvoice();
        } while (Payment::where('invoice_id', $invoiceId)->exists());
        
        return view('parent.financial.create', compact('user', 'invoiceId'));
    }

    /* ---------------------------------------
        Auto generated invoice number
    --------------------------------------- */
    private function generateInvoice(): string
    {
        $prefix = 'ITR';
        $datePart = now()->format('ymd');
        $randomChars = strtoupper(Str::random(5));
    
        return $prefix . $datePart . $randomChars;
    }

    /* ---------------------------------------
        Make payment process
    --------------------------------------- */
    public function store(Request $request) {
        try {
            $validated = $request->validate([
                'invoice_id' => 'required|string',
                'full_name' => 'required|string',
                'email_address' => 'required|email|exists:users,email',
                'phone_number' => 'required|string|regex:/^01\d{8,11}$/',
                'description' => 'required|integer|min:0',
                'amount' => 'required|numeric|min:1',
            ]);

            // Get the user & description
            $user = Auth::user();
            $description = config('payments')[$validated['description']]['name'];

            // Check if the user already make the payment
            $paymentExists = Payment::where('user_id', $user->id)->where('description', $description)->first();
            if ($paymentExists) {
                if ($paymentExists->status == 'paid') {
                    return back()->withInput()->with([
                        'result' => 'error',
                        'message' => 'You have already made a payment for this description.',
                    ]);
                } else {
                    $payUrl = env('TOYYIBPAY_URL') . $paymentExists->bill_code;
                    return redirect()->to($payUrl);
                }
            }

            // Make payment
            DB::beginTransaction();
            try {
                $payment = Payment::create([
                    'invoice_id' => $validated['invoice_id'],
                    'user_id' => $user->id,
                    'full_name' => $validated['full_name'],
                    'email' => $validated['email_address'],
                    'phone' => $validated['phone_number'],
                    'description' => config('payments')[$validated['description']]['name'],
                    'amount' => $validated['amount']
                ]);

                // Create invoice first
                $paymentGatewayResponse = $this->paymentGateway($payment);
                $billCode = $paymentGatewayResponse['0']['BillCode'];

                // Check if the payment gateway response is successful
                if (isset($paymentGatewayResponse['0']) && isset($paymentGatewayResponse['0']['BillCode'])) {
                    $billCode = $paymentGatewayResponse['0']['BillCode'];
                    DB::commit();

                    // Update the bill code to the payment data
                    $payment->update(['bill_code' => $billCode]);
    
                    // Go to payment gateway
                    $payUrl = env('TOYYIBPAY_URL') . $billCode;
                    return redirect()->to($payUrl);
                } else {
                    DB::rollBack();
                    return back()->withInput()->with([
                        'result' => 'error',
                        'message' => 'An error occurred while processing your request. Please try again.',
                    ]);
                }
            } catch (\Throwable $th) {
                DB::rollBack();
                return back()->withInput()->with([
                    'result' => 'error',
                    'message' => 'An error occurred while processing your request. Please try again.',
                ]);
            }
        } catch (ValidationException $e) {
            $errors = $e->validator->errors();
            return back()->withInput()->withErrors($errors)->with([
                'result' => 'error',
                'message' => $errors->first(),
            ]);
        }
    }

    /* ---------------------------------------
        Make payment process
    --------------------------------------- */
    private function paymentGateway($data) {
        $toyyibpayUrl = env('TOYYIBPAY_URL') . 'index.php/api/createBill';
        
        // Create bill
        $response = Http::asForm()->post($toyyibpayUrl, [
            'userSecretKey' => env('TOYYIBPAY_API_KEY'),
            'categoryCode' => env('TOYYIBPAY_CATEGORY_ID'),
            'billName' => $data->invoice_id,
            'billDescription' => $data->description,
            'billPriceSetting' => 1,
            'billPayorInfo' => 1,
            'billAmount' => $data->amount * 100,
            'billReturnUrl' => env('APP_URL').'/parent/return',
            'billCallbackUrl' => env('APP_URL').'/parent/callback',
            'billTo' => $data->full_name,
            'billEmail' => $data->email,
            'billPhone' => $data->phone,
            'billPaymentChannel' => 2,
        ]);

        // Check if the response is successful
        if ($response->successful()) {
            return $response->json();
        }
        throw new \Exception('Payment gateway error: ' . $response->body());
    }

    /* ---------------------------------------
        Return process
    --------------------------------------- */
    public function return(Request $request) {
        $toyyibpayUrl = env('TOYYIBPAY_URL') . 'index.php/api/getBillTransactions';
    
        // Get bill transactions
        $response = Http::asForm()->post($toyyibpayUrl, [
            'billCode' => $request->billcode,
        ]);

        // Get payment status
        if ($request->status_id == 1) {
            $status = 'paid';
        } else if ($request->status_id == 2) {
            $status = 'pending';
        } else {
            $status = 'failed';
        }

        // Get the payment
        $payment = Payment::where('bill_code', $request->billcode)->first();

        // Check if the response is successful
        if ($response->successful() && $payment) {
            $responseData = $response->json();

            // Array for payment update
            $paymentData = [
                'trans_charge' => $responseData[0]['transactionCharge'],
                'payment_method' => $responseData[0]['billpaymentChannel'],
                'receipt_id' => $request->transaction_id,
                'reference' => $responseData[0]['billExternalReferenceNo'] ?? null,
                'status' => $status,
                'paid_at' => now(),
            ];
            
            // Update payment details
            $payment->update($paymentData);

            // Check payment status
            if ($status == 'paid') {
                return to_route('parent.financial.index')->with([
                    'result' => 'success',
                    'message' => 'Payment successful! Thank you.',
                ]);
            } else {
                $payment->delete();
                return to_route('parent.financial.index')->with([
                    'result' => 'error',
                    'message' => 'Payment failed! Please try again.',
                ]);
            }
        } else {
            return to_route('parent.financial.index')->with([
                'result' => 'error',
                'message' => 'Something wrong happened! Please contact the administrator.',
            ]);
        }
    }

    /* ---------------------------------------
        Callback process
    --------------------------------------- */
    public function callback(Request $request) {
        // Validate incoming data
        $request->validate([
            'billcode' => 'required',
            'status_id' => 'required|integer',
            'transaction_id' => 'nullable|string',
        ]);
    
        // Determine payment status
        $status = match ($request->status_id) {
            1 => 'paid',
            2 => 'pending',
            default => 'failed',
        };
    
        // Find the payment using the billcode
        $payment = Payment::where('bill_code', $request->billcode)->first();
        if (!$payment) {
            return response('Payment not found', 404);
        }
    
        // Update payment details
        $paymentData = [
            'status' => $status,
            'receipt_id' => $request->transaction_id ?? $payment->receipt_id,
            'paid_at' => $status === 'paid' ? now() : $payment->paid_at,
        ];
        $payment->update($paymentData);
    
        // Return a 200 OK response to ToyyibPay
        return response('OK', 200);
    }
}
