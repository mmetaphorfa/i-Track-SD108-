<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Classroom;
use App\Models\Timetable;
use Illuminate\Http\Request;
use App\Models\ClassroomUser;
use App\Models\ClassroomStudent;
use App\Models\ClassroomTeacher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ClassController extends Controller
{
    /* ---------------------------------------
        Class management page
    --------------------------------------- */
    public function index() {
        $classes = Classroom::with(['creator', 'teachers'])->orderBy('level', 'asc')->orderBy('name', 'asc') ->get();

        // Get all teachers
        $teachers = User::where('admin_role', 'teacher')->get();

        return view('user.class.index', compact('classes', 'teachers'));
    }

    /* ---------------------------------------
        Create subject process
    --------------------------------------- */
    public function store(Request $request) {
        try {
            $validated = $request->validate([
                'class_code' => 'required|string|max:5|unique:subjects,code',
                'class_name' => 'required|string',
                'grade_level' => 'required|integer|min:1|max:6',
                'class_limit' => 'required|integer|min:1',
                'teacher' => 'nullable|integer|exists:users,id',
            ]);

            // Check if the same class added
            if (Classroom::where('code', $validated['class_code'])->where('name', $validated['class_name'])->where('level', $validated['grade_level'])->exists()) {
                return back()->withInput()->with([
                    'result' => 'error',
                    'message' => 'The class already exists.',
                    'section' => '#createModal',
                ]);
            }

            DB::beginTransaction();
            try {
                // Save new class
                $classroom = Classroom::create([
                    'code' => $validated['class_code'],
                    'name' => $validated['class_name'],
                    'level' => $validated['grade_level'],
                    'max_limit' => $validated['class_limit'],
                    'created_by' => Auth::user()->id,
                ]);

                // Add assigned teacher
                if (!empty($validated['teacher'])) {
                    ClassroomUser::create([
                        'classroom_id' => $classroom->id,
                        'user_id' => $validated['teacher'],
                        'assigned_at' => now(),
                    ]);
                }
                
                DB::commit();
                return to_route('classes.index')->with([
                    'result' => 'success',
                    'message' => 'New class has been added.',
                ]);
            } catch (\Throwable $th) {
                DB::rollBack();
                return back()->withInput()->with([
                    'result' => 'error',
                    'message' => 'An error occurred while processing your request. Please try again.',
                    'section' => '#createModal',
                ]);
            }
        } catch (ValidationException $e) {
            $errors = $e->validator->errors();
            return back()->withInput()->withErrors($errors)->with([
                'result' => 'error',
                'message' => $errors->first(),
                'section' => '#createModal',
            ]);
        }
    }

    /* ---------------------------------------
        Classroom details page
    --------------------------------------- */
    public function edit(string $id) {
        $classroom = Classroom::with('creator')->findOrFail($id);

        // Get all teachers
        $teachers = User::where('admin_role', 'teacher')->get();

        // Get timetable for the classroom
        $timetable = Timetable::where('class_id', $classroom->id)->where('status', 'active')->first();
        
        return view('user.class.edit', compact('classroom', 'teachers', 'timetable'));
    }

    /* ---------------------------------------
        Update classroom process
    --------------------------------------- */
    public function update(Request $request, int $id) {
        $classroom = Classroom::findOrFail($id);

        try {
            $validated = $request->validate([
                'class_code' => 'required|string|max:5',
                'class_name' => 'required|string',
                'grade_level' => 'required|integer|min:1|max:6',
                'class_limit' => 'required|integer|min:1',
                'teacher' => 'nullable|integer|exists:users,id',
            ]);

            // Check if the same class already added
            if (
                Classroom::where('code', $validated['class_code'])->where('name', $validated['class_name'])->
                    where('level', $validated['grade_level'])->whereNot('id', $classroom->id)->exists()) {
                return back()->withInput()->with([
                    'result' => 'error',
                    'message' => 'The class already exists.',
                ]);
            }

            // Save the classroom
            $classroom->update([
                'code' => $validated['class_code'],
                'name' => $validated['class_name'],
                'level' => $validated['grade_level'],
                'max_limit' => $validated['class_limit'],
            ]);

            // Get the active teacher
            if (count($classroom->teachers) > 0) {
                // Find the currently active teacher
                $activeTeacher = $classroom->teachers->first(function ($teacher) {
                    return $teacher->pivot->status === 'active';
                });
            
                // Check if the assigned teacher has changed
                if ($activeTeacher && $validated['teacher'] != $activeTeacher->id) {
                    // Set the current teacher to inactive
                    $classroom->teachers()->updateExistingPivot($activeTeacher->id, [
                        'status' => 'inactive',
                        'end_at' => now(),
                    ]);
            
                    // Assign the new teacher if provided
                    if (!empty($validated['teacher'])) {
                        ClassroomUser::create([
                            'classroom_id' => $classroom->id,
                            'user_id' => $validated['teacher'],
                            'assigned_at' => now(),
                        ]);
                    }
                } else {
                    // If no active teacher exists
                    if (!empty($validated['teacher'])) {
                        ClassroomUser::create([
                            'classroom_id' => $classroom->id,
                            'user_id' => $validated['teacher'],
                            'assigned_at' => now(),
                        ]);
                    }
                }
            } else {
                // If no active teacher exists
                if (!empty($validated['teacher'])) {
                    ClassroomUser::create([
                        'classroom_id' => $classroom->id,
                        'user_id' => $validated['teacher'],
                        'assigned_at' => now(),
                    ]);
                }
            }
            
            return to_route('classes.index')->with([
                'result' => 'success',
                'message' => 'The classroom has been updated.',
            ]);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors();
            return back()->withInput()->withErrors($errors)->with([
                'result' => 'error',
                'message' => $errors->first(),
            ]);
        }
    }

    /* ---------------------------------------
        Student class page
    --------------------------------------- */
    public function students(string $id) {
        $classroom = Classroom::with(['creator', 'students'])->findOrFail($id);

        // Get all students
        $allStudents = Student::all();

        // Filter out students with an active status in the pivot table and return only 'mykid' and 'name'
        $students = $allStudents->filter(function ($student) {
            return !ClassroomStudent::where('student_id', $student->id)
                                    ->where('status', 'active')
                                    ->exists();
        })->map(function ($student) {
            return $student->mykid . ' - ' . $student->full_name;
        })->values()->toArray();

        // Get active students in classroom
        $activeStudents = ClassroomStudent::with('student')->where('class_id', $classroom->id)->where('status', 'active')->get();
                
        return view('user.class.student', compact('classroom', 'students', 'activeStudents'));
    }

    /* ---------------------------------------
        Student enrollment process
    --------------------------------------- */
    public function enrollment(Request $request, string $id) {
        $classroom = Classroom::with(['creator', 'students'])->findOrFail($id);
        
        try {
            $validated = $request->validate([
                'students' => 'required|string',
            ]);

            // Decode JSON into an associative array
            $arrayData = json_decode($validated['students'], true);
            $studentData = array_map(function ($item) {
                return $item['value'];
            }, $arrayData);

            // Extract IDs from the value
            $studentIds = array_map(function ($item) {
                return explode(' - ', $item)[0];
            }, $studentData);

            // Query the database to check if all IDs exist
            $existingCount = Student::whereIn('mykid', $studentIds)->count();

            // Check if all IDs exist
            if ($existingCount !== count($studentIds)) {
                return back()->withInput()->with([
                    'result' => 'error',
                    'message' => 'An error occurred while processing your request. Please try again.',
                ]);
            }

            // Check maximum students allowed in one class
            $totalNewStudents = count($studentIds);
            if (($totalNewStudents + $classroom->current_limit) > $classroom->max_limit) {
                return back()->withInput()->with([
                    'result' => 'error',
                    'message' => 'Adding these students will exceed the maximum limit of the class.',
                ]);
            } else {
                DB::beginTransaction();
                try {
                    // Save new students
                    foreach ($studentIds as $key => $studentId) {
                        $student = Student::where('mykid', $studentId)->first();

                        ClassroomStudent::create([
                            'class_id' => $classroom->id,
                            'student_id' => $student->id,
                            'enrolled_at' => now(),
                        ]);
                    }

                    $classroom->current_limit = $classroom->current_limit + $totalNewStudents;
                    $classroom->save();
                    
                    DB::commit();
                    return back()->with([
                        'result' => 'success',
                        'message' => 'New students has been added to the class.',
                    ]);
                } catch (\Throwable $th) {
                    DB::rollBack();
                    return back()->withInput()->with([
                        'result' => 'error',
                        'message' => 'An error occurred while processing your request. Please try again.',
                    ]);
                }
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
        Remove student enrollment process
    --------------------------------------- */
    public function remove(Request $request, string $id) {
        $enrollment = ClassroomStudent::findOrFail($id);
        
        DB::beginTransaction();
        try {
            // Set enrollment status to inactive
            $enrollment->update([
                'status' => 'inactive',
            ]);

            // Minus one current total students in table classroom
            $classroom = Classroom::where('id', $enrollment->class_id)->first();
            $classroom->update([
                'current_limit' => $classroom->current_limit - 1,
            ]);

            DB::commit();
            return back()->with([
                'result' => 'success',
                'message' => 'The student has been removed from the class.',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with([
                'result' => 'error',
                'message' => 'An error occurred while processing your request. Please try again.',
            ]);
        }
    }

    /* ---------------------------------------
        Class teachers page
    --------------------------------------- */
    public function teachers() {
        $classes = Classroom::with(['creator', 'teachers'])->orderBy('level', 'asc')->orderBy('name', 'asc') ->get();

        return view('user.class.teachers', compact('classes'));
    }

    /* ---------------------------------------
        Class teachers edit page
    --------------------------------------- */
    public function teachersEdit(int $id) {
        $classroom = Classroom::findOrFail($id);

        // Get all subjects
        $subjects = Subject::all();
        
        return view('user.class.teacher', compact('subjects', 'classroom'));
    }

    /* ---------------------------------------
        Class teachers edit process
    --------------------------------------- */
    public function teachersUpdate(Request $request, int $id) {
        $classroom = Classroom::findOrFail($id);

        try {
            $validated = $request->validate([
                'subject_id' => 'required|array',
                'subject_id.*' => 'required|numeric|exists:subjects,id',
                'teacher_id' => 'required|array',
                'teacher_id.*' => 'nullable|numeric|exists:users,id',
            ]);

            // Assign teachers to subjects
            DB::beginTransaction();
            try {
                // Check if the teacher already assigned to the subject
                foreach ($validated['subject_id'] as $key => $subject) {
                    $existingRecord = ClassroomTeacher::where('classroom_id', $classroom->id)
                        ->where('subject_id', $subject)->first();
                    $newTeacherId = $validated['teacher_id'][$key] ?? null;
                
                    if ($existingRecord) {
                        if ($newTeacherId && $existingRecord->teacher_id !== $newTeacherId) {
                            // Assign new teacher and deactivate old assignment
                            ClassroomTeacher::create([
                                'classroom_id' => $classroom->id,
                                'subject_id' => $subject,
                                'teacher_id' => $newTeacherId,
                                'assigned_at' => now(),
                            ]);
                
                            $existingRecord->update(['status' => 'inactive']);
                        } elseif (empty($newTeacherId)) {
                            // Deactivate if no teacher is provided
                            $existingRecord->update(['status' => 'inactive']);
                        }
                    } elseif ($newTeacherId) {
                        // Create a new record if none exists
                        ClassroomTeacher::create([
                            'classroom_id' => $classroom->id,
                            'subject_id' => $subject,
                            'teacher_id' => $newTeacherId,
                            'assigned_at' => now(),
                        ]);
                    }
                }

                DB::commit();
                return back()->with([
                    'result' => 'success',
                    'message' => 'The teachers have been updated.',
                ]);
            } catch (\Throwable $th) {
                DB::rollBack();
                return back()->withInput()->with([
                    'result' => 'error',
                    'message' => $th->getMessage(),
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
