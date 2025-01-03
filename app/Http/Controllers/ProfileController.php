<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Address;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    public function index() {
        $user = Auth::user();
        
        return view('user.profile.index', compact('user'));
    }

    public function update(Request $request) {
        $user = User::findOrFail(Auth::id());

        try {
            $validated = $request->validate([
                'photo' => 'nullable|file|image|mimes:jpeg,png,jpg,gif|max:5120',
                'full_name' => 'required|string',
                'email_address' => 'required|email',
                'phone_number' => 'required|string|regex:/^01\d{8,11}$/',
                'address' => 'nullable|required_with:city,postcode,state|string',
                'city' => 'nullable|required_with:address,postcode,state|string|max:100',
                'postcode' => 'nullable|required_with:address,city,state|string|min:5|max:5|regex:/^[0-9]+$/',
                'state' => 'nullable|required_with:address,city,postcode|integer|min:1|max:16',
            ], [
                'photo.image' => 'The uploaded file must be an image.',
                'photo.mimes' => 'The image must be a file of type: jpeg, jpg, png, or gif.',
                'photo.max' => 'The image may not be greater than 5MB.',
                'address.required_with' => 'The address field is required when city, postcode, or state is filled.',
                'city.required_with' => 'The city field is required when address, postcode, or state is filled.',
                'postcode.required_with' => 'The postcode field is required when address, city, or state is filled.',
                'state.required_with' => 'The state field is required when address, city, or postcode is filled.',
            ]);

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
                    'full_name' => $validated['full_name'],
                    'email' => $validated['email_address'],
                    'phone' => $validated['phone_number'],
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
                }

                // Check if a file was uploaded
                if ($request->hasFile('photo')) {
                    $file = $request->file('photo');

                    // Upload path in `public/storage/users`
                    $uploadPath = 'users';

                    // Create folder if does not exists
                    if (!Storage::disk('public')->exists($uploadPath)) {
                        Storage::disk('public')->makeDirectory($uploadPath);
                    }

                    // Store the file with a unique name
                    $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->storeAs($uploadPath, $fileName, 'public');

                    // Save file name to database
                    $user->image = $fileName;
                    $user->save();
                }
                
                DB::commit();
                return back()->with([
                    'result' => 'success',
                    'message' => 'Your profile has been updated.',
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

    public function change(Request $request) {
        $user = User::findOrFail(Auth::id());

        try {
            $validated = $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/',
                'confirm_password' => 'required|string|same:new_password',
            ]);
            
            // Check if the current password matches the one stored in the database
            if (!Hash::check($validated['current_password'], $user->password)) {
                return back()->withInput()->withErrors(['current_password' => true])->with([
                    'result' => 'error',
                    'message' => 'The current password is incorrect.',
                ]);
            }

            // Update administrator password
            DB::beginTransaction();
            try {
                $user->update([
                    'password' => Hash::make($validated['new_password']),
                ]);
                
                DB::commit();
                return back()->with([
                    'result' => 'success',
                    'message' => 'Your new password has been updated.',
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
}
