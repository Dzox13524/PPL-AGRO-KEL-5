<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsOwner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->role !== 'owner') {
            return response()->json([
                'message' => 'Akses ditolak. Hanya owner yang diizinkan mengelola akun pengguna.',
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
