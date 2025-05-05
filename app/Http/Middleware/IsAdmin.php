<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Kiểm tra nếu người dùng là admin
        // dd($request->user());
        if (! $request->user() || ! $request->user()->isAdmin()) {
            return response()->json(['message' => 'Bạn không được phép truy cập vào tài nguyên này.'], 403);
        }
        return $next($request);
    }
}
