<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /* ---------------------------------------
        Login page
    --------------------------------------- */
    public function index() {
        return view('auth.index');
    }

    /* ---------------------------------------
        Login process
    --------------------------------------- */
    public function login(Request $request) {
        try {
            $validated = $request->validate([
                'nric_number' => 'required|string|min:12|max:12',
                'password' => 'required|string',
                'remember' => 'nullable|boolean',
                'selected_role' => 'required|string|in:parent,staff',
            ]);

            // Set to False if not remember me checkbox is not checked
            $remember = $validated['remember'] ?? false;

            // Check if user account exists
            $user = User::where('username', $validated['nric_number'])->where('status', 'active')->first();
            if ($user && ($user->user_role == 'both' || $user->user_role == $validated['selected_role'])) {
                // Check user credentials
                if (Auth::attempt(['username' => $validated['nric_number'], 'password' => $validated['password']], $remember)) {
                    if ($user->user_role == 'both') {
                        $user_role = $validated['selected_role'] == 'parent' ? 'parent' : $user->admin_role;
                    } else {
                        $user_role = $user->user_role == 'parent' ? 'parent' : $user->admin_role;
                    }
                    Session::put('role', $user_role);
                    
                    // Redirect to specified dashboard
                    return to_route('user.dashboard', $user_role);
                } else {
                    return back()->withInput()->with([
                        'result' => 'error',
                        'message' => 'Password incorrect.',
                    ]);
                }
            } else {
                return back()->withInput()->with([
                    'result' => 'error',
                    'message' => 'No account found.',
                ]);
            }
        } catch (ValidationException $e) {
            $errors = $e->validator->errors();
            return back()->withInput()->with([
                'result' => 'error',
                'message' => $errors->first(),
            ]);
        }
    }

    /* ---------------------------------------
        Logout process
    --------------------------------------- */
    public function logout() {
        Auth::logout();
        session()->flush();

        return to_route('user.index');
    }
}
