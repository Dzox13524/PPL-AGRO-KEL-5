<?php

namespace App\Http\Controllers;

use App\Services\BackendApi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\View\View;

class DiagnosisController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $auth = AuthController::resolveAuthFromRequest($request);
        if ($auth['token'] === null) {
            return redirect()->route('login');
        }

        $currentUser = $auth['user'] ?? null;
        $plants = [];
        $diagnoses = [];
        $statsByLabel = [];
        $diseases = [];
        $nutritionConditions = [];
        $apiError = null;

        try {
            $meRes = BackendApi::get('/auth/me', [], $request, $auth['token']);
            if ($meRes->status() === 401) {
                return $this->redirectUnauthorized($request);
            }
            if ($meRes->successful() && is_array($meRes->json('data'))) {
                $currentUser = $meRes->json('data');
            }

            $plantsRes = BackendApi::get('/plants', ['per_page' => 100], $request, $auth['token']);
            if ($plantsRes->status() === 401) {
                return $this->redirectUnauthorized($request);
            }
            if ($plantsRes->successful() && is_array($plantsRes->json('data'))) {
                $plants = $plantsRes->json('data');
            } else {
                $apiError = $plantsRes->json('message') ?? 'Gagal memuat data tanaman dari Backend API.';
            }

            $histRes = BackendApi::get('/diagnosis/history', ['per_page' => 100], $request, $auth['token']);
            if ($histRes->successful()) {
                $diagnoses = is_array($histRes->json('data')) ? $histRes->json('data') : [];
                $statsByLabel = is_array($histRes->json('meta.stats_by_label'))
                    ? $histRes->json('meta.stats_by_label')
                    : [];
            }

            $disRes = BackendApi::get('/diagnosis/diseases', [], $request, $auth['token']);
            if ($disRes->successful() && is_array($disRes->json('data'))) {
                $diseases = $disRes->json('data');
            }

            $nutRes = BackendApi::get('/diagnosis/nutrition-conditions', [], $request, $auth['token']);
            if ($nutRes->successful() && is_array($nutRes->json('data'))) {
                $nutritionConditions = $nutRes->json('data');
            }
        } catch (\Throwable) {
            $apiError = 'Tidak dapat terhubung ke server Backend API (port 8000).';
        }

        return view('karyawan.diagnosis.index', [
            'currentUser' => $currentUser,
            'plants' => $plants,
            'diagnoses' => $diagnoses,
            'statsByLabel' => $statsByLabel,
            'diseases' => $diseases,
            'nutritionConditions' => $nutritionConditions,
            'apiError' => $apiError,
        ]);
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
