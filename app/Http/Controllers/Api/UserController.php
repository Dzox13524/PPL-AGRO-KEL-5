<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * Display a listing of all user accounts.
     */
    public function index(): JsonResponse
    {
        $users = User::select('id', 'username', 'role', 'created_at', 'edited_at')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'message' => 'Daftar pengguna berhasil diambil.',
            'data' => $users,
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created user account.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'min:3', 'max:50', 'unique:users,username'],
            'password' => ['required', 'string', 'min:6', 'max:255'],
            'role' => ['required', 'in:owner,karyawan'],
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.min' => 'Username minimal terdiri dari 3 karakter.',
            'username.max' => 'Username maksimal terdiri dari 50 karakter.',
            'username.unique' => 'Username sudah digunakan oleh akun lain.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal terdiri dari 6 karakter.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role hanya boleh bernilai "owner" atau "karyawan".',
        ]);

        $user = User::create([
            'username' => $validated['username'],
            'password' => $validated['password'],
            'role' => $validated['role'],
        ]);

        return response()->json([
            'message' => 'Akun pengguna berhasil dibuat.',
            'data' => [
                'id' => $user->id,
                'username' => $user->username,
                'role' => $user->role,
                'created_at' => $user->created_at,
                'edited_at' => $user->edited_at,
            ],
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified user account.
     */
    public function show(string|int $id): JsonResponse
    {
        $user = User::find($id);

        if (! $user) {
            return response()->json([
                'message' => 'Pengguna tidak ditemukan.',
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'message' => 'Detail pengguna berhasil diambil.',
            'data' => [
                'id' => $user->id,
                'username' => $user->username,
                'role' => $user->role,
                'created_at' => $user->created_at,
                'edited_at' => $user->edited_at,
            ],
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified user account.
     */
    public function update(Request $request, string|int $id): JsonResponse
    {
        $user = User::find($id);

        if (! $user) {
            return response()->json([
                'message' => 'Pengguna tidak ditemukan.',
            ], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'username' => [
                'required',
                'string',
                'min:3',
                'max:50',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'password' => ['nullable', 'string', 'min:6', 'max:255'],
            'role' => ['required', 'in:owner,karyawan'],
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.min' => 'Username minimal terdiri dari 3 karakter.',
            'username.max' => 'Username maksimal terdiri dari 50 karakter.',
            'username.unique' => 'Username sudah digunakan oleh akun lain.',
            'password.min' => 'Password minimal terdiri dari 6 karakter.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role hanya boleh bernilai "owner" atau "karyawan".',
        ]);

        $user->username = $validated['username'];
        $user->role = $validated['role'];

        if (! empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->save();

        return response()->json([
            'message' => 'Akun pengguna berhasil diperbarui.',
            'data' => [
                'id' => $user->id,
                'username' => $user->username,
                'role' => $user->role,
                'created_at' => $user->created_at,
                'edited_at' => $user->edited_at,
            ],
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified user account.
     */
    public function destroy(Request $request, string|int $id): JsonResponse
    {
        $user = User::find($id);

        if (! $user) {
            return response()->json([
                'message' => 'Pengguna tidak ditemukan.',
            ], Response::HTTP_NOT_FOUND);
        }

        // Cegah owner menghapus akun miliknya sendiri yang sedang login
        if ((int) $request->user()->id === (int) $user->id) {
            return response()->json([
                'message' => 'Anda tidak dapat menghapus akun Anda sendiri.',
            ], Response::HTTP_BAD_REQUEST);
        }

        $user->delete();

        return response()->json([
            'message' => 'Akun pengguna berhasil dihapus.',
        ], Response::HTTP_OK);
    }
}
