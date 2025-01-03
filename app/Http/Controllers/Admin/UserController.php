<?php

namespace App\Http\Controllers\Admin;

use App\Models\Address;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /* ---------------------------------------
        Admin management page
    --------------------------------------- */
    public function index() {
        return view('admin.management.index');
    }

    /* ---------------------------------------
        Create admin page
    --------------------------------------- */
    public function create() {
        return view('admin.management.create');
    }

    /* ---------------------------------------
        Create admin process
    --------------------------------------- */
    public function store(Request $request) {
        try {
            $validated = $request->validate([
                'nric_number' => 'required|unique:users,username|min:12|max:12|regex:/^[0-9]+$/',
                'full_name' => 'required|string',
                'email_address' => 'required|email|unique:users,email',
                'phone_number' => 'required|string|regex:/^01\d{8,11}$/',
                'position' => 'required|string',
                'address' => 'nullable|required_with:city,postcode,state|string',
                'city' => 'nullable|required_with:address,postcode,state|string|max:100',
                'postcode' => 'nullable|required_with:address,city,state|string|min:5|max:5|regex:/^[0-9]+$/',
                'state' => 'nullable|required_with:address,city,postcode|integer|min:1|max:16',
            ], [
                'address.required_with' => 'The address field is required when city, postcode, or state is filled.',
                'city.required_with' => 'The city field is required when address, postcode, or state is filled.',
                'postcode.required_with' => 'The postcode field is required when address, city, or state is filled.',
                'state.required_with' => 'The state field is required when address, city, or postcode is filled.',
            ]);

            // Generate a temporary password
            $password = 'password';

            // Create new administrator
            DB::beginTransaction();
            try {
                $user = User::create([
                    'username' => $validated['nric_number'],
                    'password' => Hash::make($password),
                    'full_name' => $validated['full_name'],
                    'email' => $validated['email_address'],
                    'phone' => $validated['phone_number'],
                    'position' => $validated['position'],
                    'user_role' => 'staff',
                    'admin_role' => 'admin',
                ]);

                // If address fields are provided, save address information
                if (!empty($validated['address'])) {
                    Address::create([
                        'user_id' => $user->id,
                        'address' => $validated['address'],
                        'city' => $validated['city'],
                        'postcode' => $validated['postcode'],
                        'state' => $validated['state'],
                    ]);
                }
                
                DB::commit();
                return to_route('admin.management.index')->with([
                    'result' => 'success',
                    'message' => 'New administrator has been saved.',
                ]);
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
        Edit admin page
    --------------------------------------- */
    public function edit(string $id) {
        $user = User::where('id', $id)->whereNot('id', 1)->where('admin_role', 'admin')->firstOrFail();

        return view('admin.management.edit', compact('user'));
    }

    /* ---------------------------------------
        Edit admin process
    --------------------------------------- */
    public function update(Request $request, string $id) {
        $user = User::findOrFail($id);

        try {
            $validated = $request->validate([
                'nric_number' => 'required|min:12|max:12|regex:/^[0-9]+$/',
                'full_name' => 'required|string',
                'email_address' => 'required|email',
                'phone_number' => 'required|string|regex:/^01\d{8,11}$/',
                'position' => 'required|string',
                'address' => 'nullable|required_with:city,postcode,state|string',
                'city' => 'nullable|required_with:address,postcode,state|string|max:100',
                'postcode' => 'nullable|required_with:address,city,state|string|min:5|max:5|regex:/^[0-9]+$/',
                'state' => 'nullable|required_with:address,city,postcode|integer|min:1|max:16',
                'role' => 'required|string|in:admin,teacher',
                'is_parent' => 'required|string|in:yes,no',
            ], [
                'address.required_with' => 'The address field is required when city, postcode, or state is filled.',
                'city.required_with' => 'The city field is required when address, postcode, or state is filled.',
                'postcode.required_with' => 'The postcode field is required when address, city, or state is filled.',
                'state.required_with' => 'The state field is required when address, city, or postcode is filled.',
            ]);

            // Check if the new NRIC number already exists for a different user
            if (User::where('username', $validated['nric_number'])->whereNot('id', $user->id)->exists()) {
                return back()->withInput()->withErrors(['username' => true])->with([
                    'result' => 'error',
                    'message' => 'The nric number has already been taken.',
                ]);
            }

            // Check if the new email address already exists for a different user
            if (User::where('email', $validated['email_address'])->whereNot('id', $user->id)->exists()) {
                return back()->withInput()->withErrors(['email_address' => true])->with([
                    'result' => 'error',
                    'message' => 'The email address has already been taken.',
                ]);
            }

            // Update administrator
            DB::beginTransaction();
            try {
                $user->update([
                    'username' => $validated['nric_number'],
                    'full_name' => $validated['full_name'],
                    'email' => $validated['email_address'],
                    'phone' => $validated['phone_number'],
                    'position' => $validated['position'],
                    'user_role' => $validated['is_parent'] == 'yes' ? 'both' : 'staff',
                    'admin_role' => $validated['role'],
                ]);

                // If address fields are provided, save address information
                if (!empty($validated['address'])) {
                    $addressData = [
                        'address' => $validated['address'],
                        'city' => $validated['city'],
                        'postcode' => $validated['postcode'],
                        'state' => $validated['state'],
                    ];
                
                    // Update existing address or create a new address
                    if ($user->address) {
                        $addressHasChanged = array_diff_assoc($addressData, $user->address->toArray());
                        if (!empty($addressHasChanged)) {
                            $user->address->update($addressData);
                            $user->updated_at = now();
                            $user->save();
                        }
                    } else {
                        Address::create(array_merge($addressData, ['user_id' => $user->id]));
                    }
                } else {
                    // Delete user address if exists
                    if ($user->address) {
                        $user->address->delete();
                    }
                }
                
                DB::commit();
                return to_route('admin.management.index')->with([
                    'result' => 'success',
                    'message' => 'The administrator has been updated.',
                ]);
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
        Update account status
    --------------------------------------- */
    public function accessControl(Request $request, string $id) {
        $validated = $request->validate([
            'status' => 'nullable|boolean',
        ]);

        // Get the user
        $user = User::findOrFail($id);
        
        // Check if status is provided
        $status = isset($validated['status']) && $validated['status'] == 1 ? 'active' : 'inactive';

        // Update user account status
        $user->status = $status;
        $user->save();

        return back()->with([
            'result' => 'success',
            'message' => 'The administrator access has been updated.',
        ]);
    }
}
