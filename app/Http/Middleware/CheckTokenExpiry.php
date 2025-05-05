<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckTokenExpiry
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // dd($request->headers->all());
        // dd($request->user()); // Xem người dùng có được xác thực không

        $token = $request->user()?->currentAccessToken();
        // dd($token);
        if ($token && $token->expires_at && now()->greaterThan($token->expires_at)) {
            $token->delete(); // xoá token hết hạn
            // dd('token deleted');
            return response()->json(['message' => 'Token đã hết hạn'], 401);
        }
        return $next($request);
    }
}
