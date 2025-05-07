<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request): \Illuminate\Http\JsonResponse
    {

        // validated login data
        $data = $request->validated();
        $user_type = $data['user_type'];

        unset($data['user_type']);

        if( Auth::attempt($data) ){
            $user = Auth::user();

            $role = $user->roles()->where('name', $user_type)->first();

            if( !$role ){
                return response()->json([
                    'message' => sprintf("you are nont a %s", $user_type)
                ], 400);
            }

            $token = $user->createToken(now()->toString())->plainTextToken;

            return response()->json([
                'success' => 'login successfully',
                'token' => $token
            ], 200);
        }

        return response()->json([
            'message' => 'failed to login'
        ], 400);

    }

    public function logout(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'success' => 'logout successfully'
        ], 200);
    }
}
