<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->only(['name', 'email', 'password']);

        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return response()->json(['success' => true, "message" => 'Account Created Successfully']);

    }

    public function login(LoginRequest $request): JsonResponse
    {

        $request->authenticate();

        auth()->user()->tokens()->delete();

        $token = auth()->user()->createToken(config('app.token'));

        return response()->json(['success' => true, "message" => 'Account Login Successfully', 'data' => auth()->user(), 'token' => $token->plainTextToken]);

    }

    public function logout(): JsonResponse
    {
        auth()->user()->tokens()->delete();

        Auth::guard('web')->logout();

        return response()->json(['success' => true, "message" => 'Account Logout Successfully']);

    }
}
