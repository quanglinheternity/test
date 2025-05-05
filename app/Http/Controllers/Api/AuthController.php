<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // Đăng ký
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đăng ký thành công!',
            'user' => $user
        ], 201); // 201 Created
    }
     // Đăng nhập
     public function login(Request $request)
     {
         $request->validate([
             'email' => 'required|string|email',
             'password' => 'required|string',
         ]);

         $user = User::where('email', $request->email)->first();

         if (! $user || ! Hash::check($request->password, $user->password)) {
             throw ValidationException::withMessages([
                 'email' => ['Sai email hoặc mật khẩu.'],
             ]);
         }

         $token = $user->createToken('api_token')->plainTextToken;

         return response()->json([
             'access_token' => $token,
             'token_type' => 'Bearer',
         ]);
     }

     // Đăng xuất
     public function logout(Request $request)
     {
         $request->user()->currentAccessToken()->delete();

         return response()->json(['message' => 'Đã đăng xuất.']);
     }

}
