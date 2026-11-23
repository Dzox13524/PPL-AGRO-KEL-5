<?php

namespace App\Http\Controllers;

use App\Services\BackendApi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\View\View;

class PredictionController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $auth = AuthController::resolveAuthFromRequest($request);
        if ($auth['token'] === null) {
            return redirect()->route('login');
        }

        $plants = [];
        $predictions = [];
        $plantHistories = [];
        $apiError = null;

        try {
            $plantsRes = BackendApi::get('/plants', ['per_page' => 100], $request, $auth['token']);
            if ($plantsRes->status() === 401) {
                return $this->redirectUnauthorized($request);
            }
            if ($plantsRes->successful() && is_array($plantsRes->json('data'))) {
                $plants = $plantsRes->json('data');
            } else {
                $apiError = $plantsRes->json('message') ?? 'Gagal memuat data tanaman dari Backend API.';
            }

            $predRes = BackendApi::get('/growth-prediction', ['limit' => 100], $request, $auth['token']);
            if ($predRes->successful() && is_array($predRes->json('data'))) {
                $predictions = $predRes->json('data');
            }

            foreach ($plants as $plant) {
                $pid = $plant['id'] ?? null;
                if (!is_numeric($pid)) {
                    continue;
                }
                $plantId = (int) $pid;
                try {
                    $histRes = BackendApi::get("/plants/{$plantId}/growth-history", [], $request, $auth['token']);
                    $plantHistories[$plantId] = $histRes->successful() && is_array($histRes->json('data'))
                        ? $histRes->json('data')
                        : [];
                } catch (\Throwable) {
                    $plantHistories[$plantId] = [];
                }
            }
        } catch (\Throwable) {
            $apiError = 'Tidak dapat terhubung ke server Backend API (port 8000).';
        }

        return view('karyawan.prediksi.index', [
            'plants' => $plants,
            'predictions' => $predictions,
            'plantHistories' => $plantHistories,
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
