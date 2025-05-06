<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParentController extends Controller
{
    public function getStudents(User $parent): JsonResponse
    {
        if (!Auth::user()->can('view', $parent)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!$parent->hasRole('parent')) {
            return response()->json(['message' => 'User is not a parent'], 400);
        }

        $students = $parent->students()
            ->with([
                'user',
                'classrooms',
                'attendance' => function ($query) {
                    $query->latest('recorded_at');
                }
            ])
            ->get();

        return response()->json([
            'data' => $students,
            'message' => 'Students retrieved successfully'
        ]);
    }
} 