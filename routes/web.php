<?php

use App\Http\Controllers\AcademicController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TimetableController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\Admin\UserController as AdminManageController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\ForgotController;
use App\Http\Controllers\Teacher\AttendanceController;
use App\Http\Controllers\Teacher\UserController as TeacherManageController;
use App\Http\Controllers\Parent\UserController as ParentManageController;
use App\Http\Controllers\Parent\FinancialController as ParentFinancialController;
use App\Http\Controllers\Parent\StudentController as ParentStudentController;

/* ---------------------------------------
    Guest
--------------------------------------- */
Route::redirect('/', '/login');
Route::middleware('guest')->name('user.')->group(function() {
    // Log in
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::get('/login', [AuthController::class, 'index'])->name('index');

    // Forgot Password
    Route::post('/reset', [ForgotController::class, 'reset'])->name('reset');
    Route::get('/forgot-password', [ForgotController::class, 'forgot'])->name('forgot');
});

/* ---------------------------------------
    Auth (All Users)
--------------------------------------- */
Route::middleware('auth')->group(function() {
    // Log out
    Route::get('/logout', [AuthController::class, 'logout'])->name('user.logout');

    // Dashboard
    Route::get('/{role}/dashboard', [DashboardController::class, 'dashboard'])->name('user.dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('user.profile');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('user.profile.update');
    Route::patch('/password', [ProfileController::class, 'change'])->name('user.profile.change');

    // Receipt
    Route::get('/download/receipt/{id}', [ReceiptController::class, 'download'])->name('user.download.receipt');

    // AJAX
    Route::name('ajax.')->group(function() {
        Route::get('/get-data/{role}', [AjaxController::class, 'getUserData'])->name('getUserData');
        Route::get('/get-data/{role}/students', [AjaxController::class, 'getStudentData'])->name('getStudentData');
        Route::get('/get-data/{role}/payments', [AjaxController::class, 'getPayments'])->name('getPayments');
        Route::get('/get-data/attendances/{id}', [AjaxController::class, 'getAttendances'])->name('getAttendances');
    });
});

/* ---------------------------------------
    Super Admin Only
--------------------------------------- */
Route::middleware(['auth', 'role:superadmin'])->prefix('admin')->name('admin.')->group(function() {
    // Management
    Route::resource('/management/administrator', AdminManageController::class)->names([
        'index' => 'management.index',
        'create' => 'management.create',
        'store' => 'management.store',
        'show' => 'management.show',
        'edit' => 'management.edit',
        'update' => 'management.update',
    ])->except(['show', 'destroy'])->parameter('administrator', 'id');
    Route::patch('management/administrator/{id}/status', [AdminManageController::class, 'accessControl'])->name('management.uac');
    Route::patch('management/administrator/{id}/convert', [AdminManageController::class, 'convertAccount'])->name('management.convert');
});

/* ---------------------------------------
    Administrators Only
--------------------------------------- */
Route::middleware(['auth', 'role:superadmin,admin'])->group(function() {
    Route::name('teacher.')->group(function() {
        // Management
        Route::resource('{role}/management/teacher', TeacherManageController::class)->names([
            'index' => 'management.index',
            'create' => 'management.create',
            'store' => 'management.store',
            'show' => 'management.show',
            'edit' => 'management.edit',
            'update' => 'management.update',
        ])->except(['show', 'destroy'])->parameter('teacher', 'id');
        Route::patch('{role}/management/teacher/{id}/status', [TeacherManageController::class, 'accessControl'])->name('management.uac');
        Route::patch('management/teacher/{id}/convert', [TeacherManageController::class, 'convertAccount'])->name('management.convert');
        Route::patch('{role}/management/teacher/{id}/subjects', [TeacherManageController::class, 'editSubjects'])->name('management.subjects');
    });

    // Class
    Route::resource('/classes', ClassController::class)->parameter('classes', 'id')->except(['create', 'show', 'destroy']);
    Route::post('/class/{id}/students', [ClassController::class, 'enrollment'])->name('classes.enrollment');
    Route::patch('/class/{id}/students/remove', [ClassController::class, 'remove'])->name('classes.remove');
    Route::get('/class/{id}/students', [ClassController::class, 'students'])->name('classes.students');
    Route::get('/class/teachers', [ClassController::class, 'teachers'])->name('classes.teachers');
    Route::post('/class/teachers/{id}/edit', [ClassController::class, 'teachersUpdate'])->name('classes.teachers.update');
    Route::get('/class/teachers/{id}/edit', [ClassController::class, 'teachersEdit'])->name('classes.teachers.edit');

    // Subject
    Route::resource('/subjects', SubjectController::class)->parameter('subjects', 'id')->except(['create', 'edit', 'destroy']);

    // Payment
    Route::resource('/financial', FinancialController::class)->parameter('financial', 'id')->only(['index', 'show']);

    // Timetable
    Route::post('/timetable/{id}/upload', [TimetableController::class, 'upload'])->name('user.timetable.upload');

    // Announcement
    Route::resource('/announcements', AnnouncementController::class)->parameter('announcements', 'id')->except(['show']);

    // Parent Management
    Route::name('parent.')->group(function() {
        Route::resource('{role}/management/parent', ParentManageController::class)->names([
            'index' => 'management.index',
            'create' => 'management.create',
            'store' => 'management.store',
            'show' => 'management.show',
            'edit' => 'management.edit',
            'update' => 'management.update',
        ])->except(['show', 'destroy'])->parameter('parent', 'id');
        Route::patch('{role}/management/parent/{id}/status', [ParentManageController::class, 'accessControl'])->name('management.uac');
    });

    // Student Management
    Route::name('student.')->group(function() {
        Route::resource('{role}/management/student', StudentController::class)->names([
            'index' => 'management.index',
            'create' => 'management.create',
            'store' => 'management.store',
            'show' => 'management.show',
            'edit' => 'management.edit',
            'update' => 'management.update',
        ])->except(['show', 'destroy'])->parameter('student', 'id');
    });
});

/* ---------------------------------------
    Teachers Only
--------------------------------------- */
Route::middleware(['auth', 'role:teacher'])->group(function() {
    // Academic
    Route::resource('/academics', AcademicController::class)->parameter('academics', 'id')->except(['create']);

    // Attendance
    Route::resource('/attendances', AttendanceController::class)->parameter('attendances', 'id')->except(['show']);
});

/* ---------------------------------------
    Parents Only
--------------------------------------- */
Route::middleware(['auth', 'role:parent'])->prefix('parent')->name('parent.')->group(function() {
    // Financial
    Route::resource('/financial', ParentFinancialController::class)->parameter('financial', 'id')->except(['edit', 'update', 'destroy']);
    Route::get('/pay/{id}', [ParentFinancialController::class, 'pay'])->name('financial.pay');
    Route::get('/return', [ParentFinancialController::class, 'return'])->name('financial.return');
    Route::post('/callback', [ParentFinancialController::class, 'callback'])->name('financial.callback');

    // Student
    Route::resource('/students', ParentStudentController::class)->parameter('students', 'id');
    Route::get('/students/{id}/records', [ParentStudentController::class, 'records'])->name('students.records');
    Route::get('/students/{id}/records/{year}/{examination}', [ParentStudentController::class, 'results'])->name('students.results');
    
    // FAQ
    Route::get('/faq', [FaqController::class, 'index'])->name('faq.index');
});
