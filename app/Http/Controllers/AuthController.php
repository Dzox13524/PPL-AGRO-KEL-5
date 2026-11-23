<?php

namespace App\Http\Controllers;

use App\Services\BackendApi;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\View\View;

class AuthController extends Controller
{
    private const COOKIE_NAMES = ['hidara_token', 'hidara_role', 'hidara_user'];

    public static function dashboardRouteForRole(?string $role): string
    {
        return $role === 'owner' ? route('admin.dashboard') : route('karyawan.tanaman');
    }

    /**
     * Resolve active JWT token and role from cookie or Laravel session.
     * Returns ['token' => ?string, 'role' => ?string] and treats expired JWTs as unauthenticated.
     */
    public static function resolveAuthFromRequest(Request $request): array
    {
        $token = $request->cookie('hidara_token') ?: $request->session()->get('jwt_token');
        if (!is_string($token) || trim($token) === '') {
            return ['token' => null, 'role' => null];
        }

        $jwtRole = null;
        $parts = explode('.', $token);
        if (count($parts) === 3) {
            $decoded = base64_decode(strtr($parts[1], '-_', '+/'), true);
            $claims = is_string($decoded) ? json_decode($decoded, true) : null;
            if (is_array($claims)) {
                if (isset($claims['exp']) && is_numeric($claims['exp']) && (int) $claims['exp'] <= time()) {
                    return ['token' => null, 'role' => null];
                }
                if (isset($claims['role']) && is_string($claims['role'])) {
                    $jwtRole = $claims['role'];
                }
            }
        }

        $role = $request->cookie('hidara_role') ?: $request->session()->get('auth_role') ?: $jwtRole;

        return [
            'token' => $token,
            'role' => is_string($role) && $role !== '' ? $role : null,
        ];
    }

    public function showLogin(Request $request): View|RedirectResponse
    {
        $auth = self::resolveAuthFromRequest($request);
        if ($auth['token'] !== null) {
            return redirect(self::dashboardRouteForRole($auth['role']));
        }

        return view('auth.login');
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        try {
            $response = BackendApi::post('/auth/login', [
                'username' => trim($validated['username']),
                'password' => $validated['password'],
            ]);
        } catch (ConnectionException|\Throwable) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak dapat terhubung ke server Backend API (port 8000).',
            ], 503);
        }

        $token = $response->json('data.access_token');
        if ($response->successful() && is_string($token) && $token !== '') {
            $user = $response->json('data.user');
            $role = $response->json('data.role') ?? (is_array($user) ? ($user['role'] ?? 'karyawan') : 'karyawan');
            $expiresIn = (int) ($response->json('data.expires_in') ?? 43200);
            $minutes = max(1, (int) ceil($expiresIn / 60));

            $request->session()->regenerate();
            $request->session()->put([
                'jwt_token' => $token,
                'auth_role' => $role,
                'auth_user' => $user,
            ]);

            $json = response()->json([
                'status' => 'success',
                'message' => $response->json('message') ?? 'Login berhasil',
                'redirect' => self::dashboardRouteForRole(is_string($role) ? $role : null),
                'token' => $token,
                'role' => $role,
                'user' => $user,
                'expires_in' => $expiresIn,
            ]);

            $json->withCookie(Cookie::make('hidara_token', $token, $minutes, '/', null, false, false, false, 'Lax'));
            $json->withCookie(Cookie::make('hidara_role', (string) $role, $minutes, '/', null, false, false, false, 'Lax'));
            if ($user !== null) {
                $json->withCookie(Cookie::make('hidara_user', json_encode($user), $minutes, '/', null, false, false, false, 'Lax'));
            }

            return $json;
        }

        $statusCode = $response->status() >= 400 ? $response->status() : 401;

        return response()->json([
            'status' => 'error',
            'message' => $response->json('message') ?? 'Username atau password salah.',
        ], $statusCode);
    }

    public function logout(Request $request): RedirectResponse
    {
        $auth = self::resolveAuthFromRequest($request);
        if ($auth['token'] !== null) {
            try {
                BackendApi::post('/auth/logout', [], $request, $auth['token'], 5);
            } catch (\Throwable) {
                // Tetap hapus cookie dan sesi Laravel meskipun backend tidak dapat dihubungi.
            }
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $redirect = redirect()->route('login');
        foreach (self::COOKIE_NAMES as $cookieName) {
            $redirect->withCookie(Cookie::forget($cookieName, '/'));
        }

        return $redirect;
    }
}

