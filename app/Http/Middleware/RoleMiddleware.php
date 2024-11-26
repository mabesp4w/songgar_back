<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::guard('api')->user();
        if ($user && in_array($user->role, $roles)) {
            // Cek jika role adalah 'student'
            if ($user->role === 'student') {
                // Cek jika student terkait memiliki data di tabel StudentStatus
                $student = $user->student;
                if ($student && $student->studentStatus()->exists()) {
                    return response()->json(['error' => 'Akses ditolak karena status siswa'], 403);
                }
            }

            return $next($request);
        }
        return response()->json(['error' => 'Role Tidak Diijinkan'], 403);
    }
}
