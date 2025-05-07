<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClassroomResource;
use App\Models\Classroom;
use App\Http\Requests\StoreClassroomRequest;
use App\Http\Requests\UpdateClassroomRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassroomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClassroomRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Classroom $classroom)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Classroom $classroom)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClassroomRequest $request, Classroom $classroom)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Classroom $classroom)
    {
        //
    }

    public function getTeachers(Classroom $classroom): JsonResponse
    {
//        if (!Auth::user()->can('view', $classroom)) {
//            return response()->json(['message' => 'Unauthorized'], 403);
//        }

        $teachers = $classroom->teachers()
            ->with(['teacherProfile', 'schedules'])
            ->get();

        return response()->json([
            'data' => $teachers,
            'message' => 'Teachers retrieved successfully'
        ]);
    }

    public function getSchedules(Classroom $classroom): JsonResponse
    {
//        if (!Auth::user()->can('view', $classroom)) {
//            return response()->json(['message' => 'Unauthorized'], 403);
//        }

        $schedules = $classroom->schedules()
            ->with(['teacher'])
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return response()->json([
            'data' => $schedules,
            'message' => 'Schedules retrieved successfully'
        ]);
    }

    public function getStudents(Classroom $classroom): JsonResponse
    {
//        if (!Auth::user()->can('view', $classroom)) {
//            return response()->json(['message' => 'Unauthorized'], 403);
//        }

        $students = $classroom->students()
            ->with(['user', 'attendance' => function ($query) {
                $query->latest('recorded_at');
            }])
            ->get();

        return response()->json([
            'data' => $students,
            'message' => 'Students retrieved successfully'
        ]);
    }

    public function myClassrooms(Request $request){
        $user = $request->user();

        if( $user->roles()->first()?->name !== 'teacher' ){
            return response()->json([
                'message' => 'only teachers can see their classrooms'
            ]);
        }

        $classrooms = $user->classrooms()->get();

        return response()->json([
            'success' => 'succesfuly getting classrooms',
            'classrooms' => ClassroomResource::collection($classrooms)
        ]);
    }
}
