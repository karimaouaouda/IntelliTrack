<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceReportController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\ParentController;
use Illuminate\Support\Facades\Route;


// auth apis
Route::prefix('auth')
    ->controller(\App\Http\Controllers\Auth\AuthController::class)
    ->group(function(){
        Route::post('login', 'login');

        Route::post('logout', 'logout')
            ->middleware('auth:sanctum');

    });


Route::post('/attendance/record', [AttendanceController::class, 'recordAttendance']);


// Attendance Reports
Route::prefix('attendance-reports')->group(function () {
    Route::get('/{id}', [AttendanceReportController::class, 'getUserReport']);
    Route::get('/classrooms/{classroom}', [AttendanceReportController::class, 'getClassroomReport']);
    Route::get('/classrooms/{classroom}/export', [AttendanceReportController::class, 'exportClassroomReport']);
});

// Teacher Endpoints
Route::prefix('teachers')->group(function () {
    Route::get('/{teacher}/classrooms', [TeacherController::class, 'getClassrooms']);
    Route::get('/{teacher}/schedules', [TeacherController::class, 'getSchedules']);
    Route::get('/{teacher}/attendance', [TeacherController::class, 'getAttendance']);
});

// Classroom Endpoints
Route::prefix('classrooms')->group(function () {
    Route::get('/{classroom}/teachers', [ClassroomController::class, 'getTeachers']);
    Route::get('/{classroom}/schedules', [ClassroomController::class, 'getSchedules']);
    Route::get('/{classroom}/students', [ClassroomController::class, 'getStudents']);
});

// Parent Endpoints
Route::prefix('parents')->group(function () {
    Route::get('/{parent}/students', [ParentController::class, 'getStudents']);
});

// Student Endpoints
Route::prefix('students')->group(function () {
    Route::get('/{student}/attendance', [StudentController::class, 'getAttendance']);
});

// User Endpoints
Route::prefix('users')->group(function () {
    Route::get('/{user}/attendance', [AttendanceController::class, 'getUserAttendance']);
});
