<?php

namespace App\Http\Controllers\Parent;

use App\Models\Grade;
use App\Models\Record;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\AttendanceStudent;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    /* ---------------------------------------
        Student list page
    --------------------------------------- */
    public function index() {
        $students = Student::where('parent_id', Auth::id())->get();

        return view('parent.student.index', compact('students'));
    }

    /* ---------------------------------------
        Student details page
    --------------------------------------- */
    public function show(int $id) {
        $student = Student::findOrFail($id);

        // Check if student belongs to parent
        if ($student->parent_id != Auth::id()) {
            return to_route('user.dashboard', 'parent');
        }

        // Get student class
        $classroom = $student->classrooms()->where('status', 'active')->first();

        // Get previous classes
        $classrooms = $student->classrooms()->whereNot('status', 'active')->get();

        // Get timetable
        $timetable = $classroom->timetables()->where('status', 'active')->first() ?? null;

        return view('parent.student.show', compact('student', 'classroom', 'classrooms', 'timetable'));
    }

    /* ---------------------------------------
        Student records page
    --------------------------------------- */
    public function records(int $id) {
        $student = Student::findOrFail($id);

        // Check if student belongs to parent
        if ($student->parent_id != Auth::id()) {
            return to_route('user.dashboard', 'parent');
        }

        // Get attendance records
        $attendances = AttendanceStudent::where('student_id', $id)->orderBy('id', 'desc')->get();

        // Get examination records
        $grades = Grade::where('student_id', $id)->get()->groupBy('record_id');
        $gradeIds = $grades->map(function ($grades) {
            return $grades->first()->record_id;
        })->values()->all();
        $data = Record::with('classroom')->whereIn('id', $gradeIds)->orderBy('year', 'desc')->orderBy('examination', 'desc')->get();
        $records = collect($data)->unique(function ($item) {
            return $item->examination . '-' . $item->year;
        })->map(function ($item) {
            return [
                'examination' => $item->examination,
                'year' => $item->year,
                'classroom' => $item->classroom->name . ' (' . $item->classroom->level . ')',
            ];
        })->values()->all();

        return view('parent.student.records', compact('student', 'attendances', 'records'));
    }

    /* ---------------------------------------
        Student results page
    --------------------------------------- */
    public function results(int $id, int $year, int $exam) {
        $student = Student::findOrFail($id);

        // Check if student belongs to parent and get student classroom
        $classroom = $student->classrooms()->where('status', 'active')->first();
        if ($student->parent_id != Auth::id() || !$classroom) {
            return to_route('user.dashboard', 'parent');
        }

        // Get records
        $recordIds = Record::where('classroom_id', $classroom->id)->where('examination', $exam)->where('year', $year)->pluck('id');

        // Get all results
        $results = Grade::whereIn('record_id', $recordIds)->where('student_id', $student->id)->orderBy('category', 'asc')->get()->groupBy('record_id');

        return view('parent.student.results', compact('student', 'results'));
    }
}
