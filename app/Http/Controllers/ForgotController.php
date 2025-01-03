<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\ForgotMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ForgotController extends Controller
{
    public function forgot()
    {
        return view('auth.forgot');
    }

    public function reset(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string',
        ]);

        // Check if user exists
        $user = User::where('username', $validated['username'])->orWhere('email', $validated['username'])->where('status', 'active')->first();
        if (!$user) {
            return back()->withInput()->with([
                'result' => 'error',
                'message' => 'Account not found.',
            ]);
        } else {
            // Generate temporary password
            $tempPass = substr(md5(rand()), 0, 15);

            // Update user password if email sent
            DB::beginTransaction();
            try {
                // Send email
                $data = [
                    'name' => $user->full_name,
                    'password' => $tempPass,
                ];
                Mail::to($user->email)->send(new ForgotMail($data));

                // Update user password
                $user->password = Hash::make($tempPass);
                $user->save();

                DB::commit();
                return to_route('user.index')->with([
                    'result' => 'success',
                    'message' => 'Please check your email for the new password.',
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->withInput()->with([
                    'result' => 'error',
                    'message' => 'An error occurred while processing your request. Please try again.',
                ]);
            }
        }
    }
}
