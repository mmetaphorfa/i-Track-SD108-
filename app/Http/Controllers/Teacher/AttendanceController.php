<?php

namespace App\Http\Controllers\Teacher;

use Illuminate\Http\Request;
use App\Models\ClassroomUser;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceStudent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AttendanceController extends Controller
{
    /* ---------------------------------------
        Attendance management page
    --------------------------------------- */
    public function index() {
        return view('teacher.attendance.index');
    }

    /* ---------------------------------------
        Create attendance page
    --------------------------------------- */
    public function create() {
        $classrooms = ClassroomUser::with(['classroom' => function ($query) {
            $query->orderBy('level')->orderBy('name');
        }])->where('user_id', Auth::user()->id)->whereNull('end_at')->get();

        return view('teacher.attendance.create', compact('classrooms'));
    }

    /* ---------------------------------------
        Create attendance process
    --------------------------------------- */
    public function store(Request $request) {
        try {
            $validated = $request->validate([
                'class' => 'required|numeric|exists:classrooms,id',
                'date' => 'required|date|before_or_equal:today',
                'type' => 'required|numeric|min:1',
            ]);

            // Check if record already existed
            if (Attendance::where('class_id', $validated['class'])->where('date', $validated['date'])->where('type', $validated['type'])->exists()) {
                return back()->withInput()->with([
                    'result' => 'error',
                    'message' => 'The attendance record already exists.',
                ]);
            }

            DB::beginTransaction();
            try {
                // Save new attendance
                $attendance = Attendance::create([
                    'class_id' => $validated['class'],
                    'date' => $validated['date'],
                    'type' => $validated['type'],
                    'created_by' => Auth::user()->id,
                ]);
                
                DB::commit();
                return to_route('attendances.edit', $attendance->id);
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
        Edit attendance page
    --------------------------------------- */
    public function edit(int $id) {
        $attendance = Attendance::findOrFail($id);

        // Get students
        if (AttendanceStudent::where('attendance_id', $attendance->id)->exists()) {
            $students = AttendanceStudent::with('student')->where('attendance_id', $attendance->id)->get();
            $type = 'recorded';
        } else {
            $students = $attendance->class->students()->wherePivot('status', 'active')->get();
            $type = 'new';
        }

        return view('teacher.attendance.edit', compact('attendance', 'students', 'type'));
    }

    /* ---------------------------------------
        Edit attendance process
    --------------------------------------- */
    public function update(Request $request, int $id) {
        $attendance = Attendance::findOrFail($id);

        try {
            $validated = $request->validate([
                'student_id' => 'required|array',
                'student_id.*' => 'required|numeric|exists:students,id',
                'status' => 'required|array',
                'status.*' => 'required|boolean',
                'remarks' => 'required|array',
                'remarks.*' => 'nullable|string',
            ]);

            DB::beginTransaction();
            try {
                // Prepare the data for sync
                foreach ($validated['student_id'] as $key => $student_id) {
                    $record = AttendanceStudent::where('attendance_id', $attendance->id)->where('student_id', $student_id)->first();
                    if ($record) {
                        $record->update([
                            'status' => $validated['status'][$key],
                            'remarks' => $validated['remarks'][$key] ?? null,
                        ]);
                    } else {
                        AttendanceStudent::create([
                            'attendance_id' => $attendance->id,
                            'student_id' => $student_id,
                            'status' => $validated['status'][$key],
                            'remarks' => $validated['remarks'][$key] ?? null,
                        ]);
                    }
                }
                
                DB::commit();
                return back()->with([
                    'result' => 'success',
                    'message' => 'Attendance has been updated.',
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
        Delete attendance process
    --------------------------------------- */
    public function destroy(Request $request, int $id) {
        $attendance = Attendance::findOrFail($id);
        $attendance->delete();
        
        return back()->with([
            'result' => 'success',
            'message' => 'Attendance has been deleted.',
        ]);
    }
}
