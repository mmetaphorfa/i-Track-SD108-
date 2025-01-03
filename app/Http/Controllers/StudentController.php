<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StudentController extends Controller
{
    /* ---------------------------------------
        Student management page
    --------------------------------------- */
    public function index() {
        return view('student.management.index');
    }

    /* ---------------------------------------
        Create student page
    --------------------------------------- */
    public function create(string $role) {
        $parents = User::whereNot('user_role', 'staff')->get();
        
        return view('student.management.create', compact('parents'));
    }

    /* ---------------------------------------
        Create student process
    --------------------------------------- */
    public function store(Request $request, string $role) {
        try {
            $validated = $request->validate([
                'parent' => 'required|exists:users,id',
                'mykid_number' => 'required|unique:students,mykid|min:12|max:12|regex:/^[0-9]+$/',
                'full_name' => 'required|string',
                'email_address' => 'required|email|unique:students,email',
                'date_of_birth' => 'required|date',
                'gender' => 'required|string|in:male,female',
                'race' => 'required|integer',
                'religion' => 'required|integer',
            ]);

            // Generate student ID
            do {
                $studentId = date('y') . $this->generateId(8);
            } while (Student::where('student_id', $studentId)->exists());

            // Create new administrator
            DB::beginTransaction();
            try {
                $student = Student::create([
                    'student_id' => $studentId,
                    'mykid' => $validated['mykid_number'],
                    'full_name' => $validated['full_name'],
                    'email' => $validated['email_address'],
                    'dob' => $validated['date_of_birth'],
                    'gender' => $validated['gender'],
                    'race' => $validated['race'],
                    'religion' => $validated['religion'],
                    'parent_id' => $validated['parent'],
                ]);
                
                DB::commit();
                return to_route('student.management.index', $role)->with([
                    'result' => 'success',
                    'message' => 'New student has been added.',
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
        Generate student ID
    --------------------------------------- */
    private function generateId($length = 10) {
        // First digit must be non-zero
        $randomNumber = random_int(1, 9);
    
        // Generate the rest of the digits
        for ($i = 1; $i < $length; $i++) {
            $randomNumber .= random_int(0, 9);
        }
        return $randomNumber;
    }

    /* ---------------------------------------
        Edit student page
    --------------------------------------- */
    public function edit(string $role, int $id) {
        $student = Student::with('parent')->findOrFail($id);

        // Get all parents
        $parents = User::whereNot('user_role', 'staff')->get();
        
        return view('student.management.edit', compact('student', 'parents'));
    }

    /* ---------------------------------------
        Edit student process
    --------------------------------------- */
    public function update(Request $request, string $role, int $id) {
        $student = Student::with('parent')->findOrFail($id);

        try {
            $validated = $request->validate([
                'parent' => 'required|exists:users,id',
                'mykid_number' => 'required|min:12|max:12|regex:/^[0-9]+$/',
                'full_name' => 'required|string',
                'email_address' => 'required|email',
                'date_of_birth' => 'required|date',
                'gender' => 'required|string|in:male,female',
                'race' => 'required|integer',
                'religion' => 'required|integer',
            ]);

            // Check if the new NRIC number already exists for a different student
            if (Student::where('mykid', $validated['mykid_number'])->whereNot('id', $student->id)->exists()) {
                return back()->withInput()->withErrors(['mykid_number' => true])->with([
                    'result' => 'error',
                    'message' => 'The MyKID number has already been taken.',
                ]);
            }

            // Check if the new email address already exists for a different student
            if (Student::where('email', $validated['email_address'])->whereNot('id', $student->id)->exists()) {
                return back()->withInput()->withErrors(['email_address' => true])->with([
                    'result' => 'error',
                    'message' => 'The email address has already been taken.',
                ]);
            }

            // Create new administrator
            DB::beginTransaction();
            try {
                $student->update([
                    'mykid' => $validated['mykid_number'],
                    'full_name' => $validated['full_name'],
                    'email' => $validated['email_address'],
                    'dob' => $validated['date_of_birth'],
                    'gender' => $validated['gender'],
                    'race' => $validated['race'],
                    'religion' => $validated['religion'],
                    'parent_id' => $validated['parent'],
                ]);
                
                DB::commit();
                return to_route('student.management.index', $role)->with([
                    'result' => 'success',
                    'message' => 'The student has been updated.',
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
