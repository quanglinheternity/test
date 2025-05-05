<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

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
        // Tạo access token (hạn ngắn)
         $token = $user->createToken('access_token');
         $plain = $token->plainTextToken;
         $token->accessToken->expires_at = Carbon::now()->addHour();
         $token->accessToken->save();
        // Tạo refresh token (dùng riêng để làm mới access token)
         $refreshToken = $user->createToken('refresh_token')->plainTextToken;
         return response()->json([
             'access_token' => $plain,
             'refresh_token' => $refreshToken,
             'token_type' => 'Bearer',
         ]);
     }
     // Làm mơi access token
     public function refresh(Request $request)
     {
         $refreshToken = $request->bearerToken();

         // Tìm token
         $token = PersonalAccessToken::findToken($refreshToken);

         if (! $token || $token->name !== 'refresh_token') {
             return response()->json(['message' => 'Refresh token không hợp lệ'], 401);
         }

         $user = $token->tokenable;

         // (Tuỳ chọn) Xoá token cũ để tránh reuse
         $token->delete();
         $user->tokens()->where('name', 'access_token')->delete();


         // Tạo token mới
         $newAccessToken = $user->createToken('access_token');
         $plainNewTextToken = $newAccessToken->plainTextToken;
         $newAccessToken->accessToken->expires_at = Carbon::now()->addHour();
         $newAccessToken->accessToken->save();
         $newRefreshToken = $user->createToken('refresh_token')->plainTextToken;

         return response()->json([
             'access_token' => $plainNewTextToken,
             'refresh_token' => $newRefreshToken,
         ]);
     }


     // Đăng xuất
     public function logout(Request $request)
     {
        // Xoá token đang dùng (access token)
         $request->user()->currentAccessToken()->delete();
          // (Nếu muốn chắc chắn) cũng xoá hết refresh token luôn:
         $request->user()->tokens()->where('name', 'refresh_token')->delete();
         return response()->json(['message' => 'Đã đăng xuất.']);
     }

}
