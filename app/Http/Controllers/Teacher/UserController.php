<?php

namespace App\Http\Controllers\Teacher;

use App\Models\User;
use App\Models\Address;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\ClassroomUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /* ---------------------------------------
        Teacher management page
    --------------------------------------- */
    public function index() {
        return view('teacher.management.index');
    }

    /* ---------------------------------------
        Create teacher page
    --------------------------------------- */
    public function create(string $role) {
        return view('teacher.management.create');
    }

    /* ---------------------------------------
        Create teacher process
    --------------------------------------- */
    public function store(Request $request, string $role) {
        try {
            $validated = $request->validate([
                'nric_number' => 'required|unique:users,username|min:12|max:12|regex:/^[0-9]+$/',
                'full_name' => 'required|string',
                'email_address' => 'required|email|unique:users,email',
                'phone_number' => 'required|string|regex:/^01\d{8,11}$/',
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
                    'user_role' => 'staff',
                    'admin_role' => 'teacher',
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
                return to_route('teacher.management.index', $role)->with([
                    'result' => 'success',
                    'message' => 'New teacher has been added.',
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
        Edit teacher page
    --------------------------------------- */
    public function edit(string $role, string $id) {
        $user = User::where('id', $id)->whereNot('id', 1)->where('admin_role', 'teacher')->firstOrFail();

        // Get user subjects
        $userSubjects = isset($user->subjects) ? $user->subjects->pluck('name')->toArray() : [];

        // Get all subjects
        $subjects = Subject::pluck('name')->all();

        return view('teacher.management.edit', compact('user', 'userSubjects', 'subjects'));
    }

    /* ---------------------------------------
        Edit teacher process
    --------------------------------------- */
    public function update(Request $request, string $role, string $id) {
        $user = User::findOrFail($id);

        try {
            $validated = $request->validate([
                'nric_number' => 'required|min:12|max:12|regex:/^[0-9]+$/',
                'full_name' => 'required|string',
                'email_address' => 'required|email',
                'phone_number' => 'required|string|regex:/^01\d{8,11}$/',
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

            // Update teacher
            DB::beginTransaction();
            try {
                // Check if the teacher was assigned in the classroom
                $classrooms = ClassroomUser::where('user_id', $user->id)->get();
                if ($classrooms && $user->admin_role != $validated['role']) {
                    foreach ($classrooms as $classroom) {
                        $classroom->update([
                            'status' => 'inactive',
                            'end_at' => now(), 
                        ]);
                    }
                }

                // User information
                $user->update([
                    'username' => $validated['nric_number'],
                    'full_name' => $validated['full_name'],
                    'email' => $validated['email_address'],
                    'phone' => $validated['phone_number'],
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
                return to_route('teacher.management.index', $role)->with([
                    'result' => 'success',
                    'message' => 'The teacher has been updated.',
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
    public function accessControl(Request $request, string $role, string $id) {
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
            'message' => 'The teacher access has been updated.',
        ]);
    }

    /* ---------------------------------------
        Update subject list
    --------------------------------------- */
    public function editSubjects(Request $request, string $role, string $id) {
        $user = User::findOrFail($id);

        try {
            $validated = $request->validate([
                'subjects' => 'required|string',
            ]);
            
            // Convert to an array
            $subjectsArray = json_decode($validated['subjects'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withInput()->withErrors(['subjects' => true])->with([
                    'result' => 'error',
                    'message' => 'Invalid JSON format.',
                ]);
            }

            // Extract subject names
            $subjectNames = array_column($subjectsArray, 'value');

            // Retrieve subject IDs
            $subjectIds = Subject::whereIn('name', $subjectNames)->pluck('id', 'name')->toArray();

            // Loop through each subject and check existence in the database
            foreach ($subjectNames as $subject) {
                $exists = Subject::where('name', $subject)->exists();
                if (!$exists) {
                    return back()->withInput()->withErrors(['subjects' => true])->with([
                        'result' => 'error',
                        'message' => "The subject '{$subject}' does not exist in the database.",
                    ]);
                }
            }

            // Sync the subjects with the user
            $user->subjects()->sync($subjectIds);

            return back()->with([
                'result' => 'success',
                'message' => 'The teaching subjects has been updated.',
            ]);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors();
            return back()->withInput()->withErrors($errors)->with([
                'result' => 'error',
                'message' => $errors->first(),
            ]);
        }
    }
}
