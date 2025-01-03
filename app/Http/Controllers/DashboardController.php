<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Announcement;
use Illuminate\Http\Request;
use App\Models\ClassroomTeacher;
use App\Http\Controllers\Controller;
use App\Models\AttendanceStudent;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /* ---------------------------------------
        Dashboard page
    --------------------------------------- */
    public function dashboard(string $role) {
        // Check user's role
        if ($role != session('role')) {
            return to_route('user.dashboard', session('role'));
        }

        // Administrators
        if (session('role') === 'admin') {
            // Total users
            $totalAdmins = User::where('admin_role', 'admin')->whereNot('id', 1)->count();
            $totalTeachers = User::where('admin_role', 'teacher')->whereNot('id', 1)->count();
            $totalParents = User::whereNot('user_role', 'staff')->whereNot('id', 1)->count();
            $totalStudents = Student::all()->count();
            $totalUsers = [
                [
                    'name' => 'Administrators',
                    'value' => $totalAdmins,
                    'icon' => 'user', 
                    'url' => route('admin.management.index'),
                ], [
                    'name' => 'Teachers',
                    'value' => $totalTeachers,
                    'icon' => 'agenda', 
                    'url' => route('teacher.management.index', 'admin'),
                ], [
                    'name' => 'Parents',
                    'value' => $totalParents,
                    'icon' => 'face-smile', 
                    'url' => route('parent.management.index', 'admin'),
                ], [
                    'name' => 'Students',
                    'value' => $totalStudents,
                    'icon' => 'heart', 
                    'url' => route('student.management.index', 'admin'),
                ],
            ];

            // Latest payment
            $payments = Payment::where('status', 'paid')->orderBy('paid_at', 'desc')->limit(5)->get();

            // Latest students
            $students = Student::with('parent')->orderBy('created_at', 'desc')->limit(5)->get();

            // Get latest events
            $colors = ['#ff9f89', '#89c2ff', '#91ff89', '#ffc107', '#6f42c1'];
            $announcements = Announcement::where('status', 'published')
                ->select('title', 'start_at', 'end_at')
                ->get()
                ->map(function ($announcement) use ($colors) {
                    return [
                        'title' => $announcement->title,
                        'start' => Carbon::parse($announcement->start_at)->format('Y-m-d'),
                        'end' => Carbon::parse($announcement->end_at)->format('Y-m-d'),
                        'color' => $colors[array_rand($colors)],
                    ];
                })->toArray();

            return view('admin.dashboard', compact('totalUsers', 'payments', 'students', 'announcements'));
        }

        // Teachers
        if (session('role') === 'teacher') {
            // Get all published announcements
            $announcements = Announcement::where('status', 'published')->get();

            // Get the teacher's students and total students
            $classrooms = ClassroomTeacher::where('teacher_id', Auth::id())
                ->with('classroom.students')
                ->get()
                ->map(function ($classroom) {
                    return [
                        'classroom' => $classroom->classroom->level . ' ' . $classroom->classroom->code,
                        'students' => $classroom->classroom->students->count(),
                    ];
                });

            // Remove same classroom
            $classrooms = $classrooms->unique('classroom')->values();

            // Get latest events
            $colors = ['#ff9f89', '#89c2ff', '#91ff89', '#ffc107', '#6f42c1'];
            $events = Announcement::where('status', 'published')
                ->select('title', 'start_at', 'end_at')
                ->get()
                ->map(function ($event) use ($colors) {
                    return [
                        'title' => $event->title,
                        'start' => Carbon::parse($event->start_at)->format('Y-m-d'),
                        'end' => Carbon::parse($event->end_at)->format('Y-m-d'),
                        'color' => $colors[array_rand($colors)],
                    ];
                })->toArray();

            return view('teacher.dashboard', compact('announcements', 'events', 'classrooms'));
        }

        // Parent
        if (session('role') === 'parent') {
            // Get all published announcements
            $announcements = Announcement::where('status', 'published')->get();

            // Get the students and attendance
            $studentIds = Student::where('parent_id', Auth::id())->pluck('id');
            $attendances = AttendanceStudent::with('student')->whereIn('student_id', $studentIds)->orderBy('created_at', 'desc')->limit(5)->get();

            // Get latest events
            $colors = ['#ff9f89', '#89c2ff', '#91ff89', '#ffc107', '#6f42c1'];
            $events = Announcement::where('status', 'published')
                ->select('title', 'start_at', 'end_at')
                ->get()
                ->map(function ($event) use ($colors) {
                    return [
                        'title' => $event->title,
                        'start' => Carbon::parse($event->start_at)->format('Y-m-d'),
                        'end' => Carbon::parse($event->end_at)->format('Y-m-d'),
                        'color' => $colors[array_rand($colors)],
                    ];
                })->toArray();

            return view('parent.dashboard', compact('announcements', 'events', 'attendances'));
        }
    }
}
