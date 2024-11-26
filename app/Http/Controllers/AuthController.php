<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    function login(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|string|email|max:255',
                'password' => 'required',
            ],
            [
                'email.required' => 'Email harus diisi',
                'password.required' => 'Password harus diisi',
                'email.email' => 'Email tidak valid',
                'email.max' => 'Email maksimal 255 karakter',
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        // check email and password
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status' => false,
                'message' => 'Kombinasi email dan password salah',
            ], 401);
        }
        // mengambil email
        $user = User::where('email', $request['email'])->firstOrFail();
        $role = $user->role;
        $token = $user->createToken('smartspartacus')->accessToken;

        if ($role === 'teacher') {
            // user with teacher
            $user = $user->teacher;
        } else if ($role === 'student') {
            // user with student
            $user = $user->student;
        } else {
            // user with admin
            $user = "";
        }
        // membuat token

        return response()->json([
            'status' => true,
            'role' => $role,
            'token' => $token,
            'user' => $user
        ]);
    }

    function cekToken(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'status' => true,
            'role' => $user->role
        ]);
    }


    function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'status' => true,
            'message' => 'Logout Berhasil',
        ]);
    }
}
