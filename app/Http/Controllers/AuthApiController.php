<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except('login');
    }

    public function login(Request $request)
    {
        // Validate login credentials
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string'
        ]);

        // check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'message' => 'Please fix the errors',
                'status' => 'error'
            ], 500);
        }

        // if validation passed gather login credentials
        $credentials = $request->only('email', 'password');

        if (! Auth::attempt($credentials)) {
            return response()->json([
                'data' => null,
                'message' => 'Invalid login details',
                'status' => 'error',
            ], 422);
        }

        $token = Auth::user()->createToken('authToken')->accessToken;

        return response()->json([
            'data' => [
                'token' => $token,
                'user' => new UserResource(Auth::user()),
            ],
            'message' => 'Login Successful',
            'status' => 'success'
        ], 200);
    }
}
