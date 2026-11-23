<?php

namespace App\Http\Controllers;

use App\Services\BackendApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $auth = AuthController::resolveAuthFromRequest($request);
        if ($auth['token'] === null) {
            return redirect()->route('login');
        }

        if ($auth['role'] !== 'owner') {
            return redirect()->route('karyawan.tanaman');
        }

        $employees = [];
        $apiError = null;

        try {
            $res = BackendApi::get('/employees', ['per_page' => 100], $request, $auth['token']);
            if ($res->status() === 401) {
                return $this->redirectUnauthorized($request);
            }
            if ($res->successful() && is_array($res->json('data'))) {
                $employees = $res->json('data');
            } else {
                $apiError = $res->json('message') ?? 'Gagal memuat daftar karyawan dari Backend API.';
            }
        } catch (\Throwable) {
            $apiError = 'Tidak dapat terhubung ke server Backend API (port 8000).';
        }

        return view('admin.karyawan.index', [
            'employees' => $employees,
            'totalEmployees' => count($employees),
            'apiError' => $apiError,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $auth = AuthController::resolveAuthFromRequest($request);
        if ($auth['token'] === null || $auth['role'] !== 'owner') {
            return response()->json(['status' => 'error', 'message' => 'Tidak terautentikasi.'], 401);
        }

        $validated = $request->validate([
            'nama' => ['nullable', 'string', 'max:100'],
            'username' => ['required', 'string', 'min:3', 'max:50'],
            'password' => ['required', 'string', 'min:6', 'max:72'],
        ]);

        $username = trim($validated['username']);
        $nama = trim((string) ($validated['nama'] ?? ''));

        try {
            $res = BackendApi::post('/employees', [
                'nama' => $nama !== '' ? $nama : $username,
                'username' => $username,
                'password' => $validated['password'],
                'role' => 'karyawan',
            ], $request, $auth['token']);
        } catch (\Throwable) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak dapat terhubung ke Backend API (port 8000).',
            ], 503);
        }

        return response()->json($res->json() ?? ['status' => 'error'], $res->status());
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $auth = AuthController::resolveAuthFromRequest($request);
        if ($auth['token'] === null || $auth['role'] !== 'owner') {
            return response()->json(['status' => 'error', 'message' => 'Tidak terautentikasi.'], 401);
        }

        $validated = $request->validate([
            'nama' => ['nullable', 'string', 'max:100'],
            'username' => ['required', 'string', 'min:3', 'max:50'],
            'password' => ['nullable', 'string', 'min:6', 'max:72'],
        ]);

        $payload = [
            'username' => trim($validated['username']),
        ];
        $nama = trim((string) ($validated['nama'] ?? ''));
        if ($nama !== '') {
            $payload['nama'] = $nama;
        }
        if (!empty($validated['password'])) {
            $payload['password'] = $validated['password'];
        }

        try {
            $res = BackendApi::put("/employees/{$id}", $payload, $request, $auth['token']);
        } catch (\Throwable) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak dapat terhubung ke Backend API (port 8000).',
            ], 503);
        }

        return response()->json($res->json() ?? ['status' => 'error'], $res->status());
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $auth = AuthController::resolveAuthFromRequest($request);
        if ($auth['token'] === null || $auth['role'] !== 'owner') {
            return response()->json(['status' => 'error', 'message' => 'Tidak terautentikasi.'], 401);
        }

        try {
            $res = BackendApi::delete("/employees/{$id}", [], $request, $auth['token']);
        } catch (\Throwable) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak dapat terhubung ke Backend API (port 8000).',
            ], 503);
        }

        return response()->json($res->json() ?? ['status' => 'error'], $res->status());
    }

    private function redirectUnauthorized(Request $request): RedirectResponse
    {
        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        $redirect = redirect()->route('login');
        foreach (['hidara_token', 'hidara_role', 'hidara_user'] as $name) {
            $redirect->withCookie(Cookie::forget($name, '/'));
        }

        return $redirect;
    }
}
