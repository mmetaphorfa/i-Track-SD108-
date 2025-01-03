<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Record;
use App\Models\Student;
use App\Models\Classroom;
use Illuminate\Http\Request;
use App\Models\ClassroomStudent;
use App\Models\ClassroomTeacher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AcademicController extends Controller
{
    /* ---------------------------------------
        Academic management page
    --------------------------------------- */
    public function index() {
        // Get the teacher's classes
        $classes = ClassroomTeacher::with(['classroom', 'subject'])->where('teacher_id', Auth::id())->where('status', 'active')->get();

        return view('teacher.academic.index', compact('classes'));
    }

    /* ---------------------------------------
        Academic show page
    --------------------------------------- */
    public function show(int $id) {
        $classroom = ClassroomTeacher::findOrFail($id);

        // Get the academic records
        $records = Record::where('classroom_id', $classroom->classroom_id)->where('subject_id', $classroom->subject_id)
            ->orderBy('year', 'desc')->orderBy('examination', 'asc')->get();

        return view('teacher.academic.show', compact('classroom', 'records'));
    }

    /* ---------------------------------------
        Create academic record process
    --------------------------------------- */
    public function store(Request $request) {
        try {
            $validated = $request->validate([
                'subject_id' => 'required|integer|exists:subjects,id',
                'class_id' => 'required|integer|exists:classrooms,id',
                'examination' => 'required|integer|min:1',
                'year' => 'required|integer|min:2024|max:2050',
            ]);

            // Check if record already existed
            if (Record::where('classroom_id', $validated['class_id'])->where('subject_id', $validated['subject_id'])
                ->where('examination', $validated['examination'])->where('year', $validated['year'])->exists()) {
                return back()->withInput()->with([
                    'result' => 'error',
                    'message' => 'The academic record already exists.',
                ]);
            }

            DB::beginTransaction();
            try {
                // Save new record
                $record = Record::create([
                    'teacher_id' => Auth::user()->id,
                    'subject_id' => $validated['subject_id'],
                    'classroom_id' => $validated['class_id'],
                    'examination' => $validated['examination'],
                    'year' => $validated['year'],
                ]);
                
                DB::commit();
                return to_route('academics.edit', $record->id);
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
        Academic edit page
    --------------------------------------- */
    public function edit(int $id) {
        $record = Record::findOrFail($id);

        // Get students
        if (Grade::where('record_id', $record->id)->exists()) {
            $students = Student::all();
            $studentIds = Grade::where('record_id', $record->id)->pluck('student_id');

            // Remove duplicate students
            $studentIds = $studentIds->unique();

            // Get students
            $students = ClassroomStudent::with('student')->where('class_id', $record->classroom_id)
                ->where('status', 'active')->whereIn('student_id', $studentIds)->get();
            $type = 'recorded';
        } else {
            $students = ClassroomStudent::with('student')->where('class_id', $record->classroom_id)->where('status', 'active')->get();
            $type = 'new';
        }

        return view('teacher.academic.edit', compact('type', 'record', 'students'));
    }

    /* ---------------------------------------
        Academic update process
    --------------------------------------- */
    public function update(Request $request, int $id) {
        $record = Record::findOrFail($id);

        try {
            $validated = $request->validate([
                'student_id' => 'required|array',
                'student_id.*' => 'required|integer|exists:students,id', 
                'category' => 'required|array',
                'grades' => 'required|array',
                'grades.*' => 'required|array',
                'grades.*.*' => 'required|string',
                'remarks' => 'required|array',
                'remarks.*' => 'required|array',
                'remarks.*.*' => 'required|string|max:255',
            ]);

            // return $validated;

            DB::beginTransaction();
            try {
                // Update grades
                foreach ($validated['student_id'] as $key => $student_id) {
                    foreach ($validated['category'][$key] as $cat_key => $category) {
                        // Check if record already existed
                        $grade = Grade::where('record_id', $record->id)->where('student_id', $student_id)->where('category', $category)->first();
                        if ($grade) {
                            $grade->update([
                                'grade' => $validated['grades'][$key][$cat_key],
                                'remarks' => $validated['remarks'][$key][$cat_key],
                            ]);
                        } else {
                            Grade::create([
                                'record_id' => $record->id,
                                'student_id' => $student_id,
                                'category' => $category,
                                'grade' => $validated['grades'][$key][$cat_key],
                                'remarks' => $validated['remarks'][$key][$cat_key],
                            ]);
                        }
                    }
                }

                DB::commit();
                return back()->with([
                    'result' => 'success',
                    'message' => 'Academic record has been updated successfully.',
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
        Academic destroy process
    --------------------------------------- */
    public function destroy(Request $request, int $id) {
        $record = Record::findOrFail($id);

        // If no grades, delete record
        if (!Grade::where('record_id', $record->id)->exists()) {
            DB::beginTransaction();
            try {
                $record->delete();

                DB::commit();
                return back()->with([
                    'result' => 'success',
                    'message' => 'Academic record has been deleted successfully.',
                ]);
            } catch (\Throwable $th) {
                DB::rollBack();
                return back()->with([
                    'result' => 'error',
                    'message' => 'An error occurred while processing your request. Please try again.',
                ]);
            }
        } else {
            return back()->with([
                'result' => 'error',
                'message' => 'Academic record cannot be deleted as it has grades recorded.',
            ]);
        }
    }
}
