<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\ClassroomUser;
use Illuminate\Support\Facades\Auth;

class AjaxController extends Controller
{
    /* ---------------------------------------
        Get admin/teacher/parent data
    --------------------------------------- */
    public function getUserData(string $role)
    {
        // Determine the correct role filter
        $query = User::whereNot('id', 1);
        in_array($role, ['admin', 'teacher']) ? $query->where('admin_role', $role) : $query->where('user_role', 'both')->orWhere('user_role', 'parent');
        $users = $query->orderBy('created_at', 'desc')->get();

        // Get total users
        $total_users = $users->count();
        $total_active = $users->where('status', 'active')->count();
        $total_inactive = $users->where('status', 'inactive')->count();

        // Convert to DataTable format
        $data = [];
        foreach ($users as $key => $user) {
            // Check current user
            $access = true;
            if (Auth::user()->id != 1) {
                if (
                    (session('role') == 'admin' && $user->admin_role == 'admin') || 
                    (session('role') == 'teacher' && in_array($user->admin_role, ['admin', 'teacher']))
                ) {
                    $access = false;
                }
            }

            // User data array
            $data[] = [
                'num' => $key + 1,
                'id' => $user->id,
                'nric' => $user->username,
                'name' => $user->full_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'status' => $user->status,
                'access' => $access,
            ];
        };

        return response()->json([
            'total' => number_format($total_users),
            'active' => number_format($total_active),
            'inactive' => number_format($total_inactive),
            'data' => $data,
        ]);
    }

    /* ---------------------------------------
        Get student data
    --------------------------------------- */
    public function getStudentData(string $role)
    {
        $students = Student::all();

        // Convert to DataTable format
        $data = [];
        foreach ($students as $key => $student) {
            if ($student->enrollments && count($student->enrollments) > 0) {
                $activeEnrollments = $student->enrollments()->where('status', 'active')->first();
                $class = $activeEnrollments ? $activeEnrollments->name . ' (' . $activeEnrollments->level . ')' : 'N/A';
            } else {
                $class = 'N/A';
            }

            // User data array
            $data[] = [
                'num' => $key + 1,
                'id' => $student->id,
                'student_id' => $student->student_id,
                'mykid' => $student->mykid,
                'name' => $student->full_name,
                'class' => $class,
            ];
        };

        return response()->json([
            'data' => $data,
        ]);
    }

    /* ---------------------------------------
        Get payments data
    --------------------------------------- */
    public function getPayments(string $role)
    {
        if ($role != 'parent') {
            // Get all parents payments by year
            $payments = Payment::with('user')
                ->whereHas('user', function ($query) {
                    $query->where('user_role', '!=', 'staff');
                })
                ->whereYear('created_at', now()->year)
                ->get();

            // Get total payments
            $total_payments = $payments->count();
            $total_success = $payments->where('status', 'paid')->count();
            $total_pending = $payments->where('status', 'pending')->count();
            $total_failed = $payments->where('status', 'failed')->count();

            // Get all parents payments
            $payments = Payment::with('user')
                ->whereHas('user', function ($query) {
                    $query->where('user_role', '!=', 'staff');
                })->orderBy('updated_at', 'desc')->get();

            // Convert to DataTable format
            $data = [];
            foreach ($payments as $key => $payment) {
                // User data array
                $data[] = [
                    'num' => $key + 1,
                    'id' => $payment->id,
                    'invoice' => $payment->invoice_id,
                    'name' => $payment->user->full_name,
                    'description' => $payment->description,
                    'amount' => number_format($payment->amount, 2),
                    'status' => $payment->status,
                ];
            };

            return response()->json([
                'total' => number_format($total_payments),
                'success' => number_format($total_success),
                'pending' => number_format($total_pending),
                'failed' => number_format($total_failed),
                'data' => $data,
            ]);
        }
    }

    /* ---------------------------------------
        Get attendance data
    --------------------------------------- */
    public function getAttendances(int $id)
    {
        // Get manageable classes by the teacher
        $classrooms = ClassroomUser::with('classroom')->where('user_id', $id)->whereNull('end_at')->get();

        // Extract the class IDs
        $classIds = $classrooms->pluck('classroom.id')->toArray();

        // Get the attendance records for the extracted class IDs
        $attendances = Attendance::with('class')->whereIn('class_id', $classIds)->orderBy('date', 'desc')->get();

        // Convert to DataTable format
        $data = [];
        foreach ($attendances as $key => $attendance) {
            // Attendance data array
            $data[] = [
                'num' => $key + 1,
                'id' => $attendance->id,
                'class' => $attendance->class->name . ' (' . $attendance->class->level . ')',
                'date' => date('d/m/Y', strtotime($attendance->date)),
                'type' => config('attendances.' . $attendance->type),
            ];
        };

        return response()->json([
            'data' => $data,
        ]);
    }
}
