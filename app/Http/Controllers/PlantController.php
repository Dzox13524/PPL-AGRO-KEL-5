<?php

namespace App\Http\Controllers;

use App\Services\BackendApi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\View\View;

class PlantController extends Controller
{
    private const ACTIVE_STATUSES = ['pembibitan', 'pertumbuhan', 'siap_panen'];

    public function index(Request $request): View|RedirectResponse
    {
        $auth = AuthController::resolveAuthFromRequest($request);
        if ($auth['token'] === null) {
            return redirect()->route('login');
        }

        $plants = [];
        $diagnosisItems = [];
        $diagnosisStats = [];
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

            $diagRes = BackendApi::get('/diagnosis/history', ['per_page' => 50], $request, $auth['token']);
            if ($diagRes->successful()) {
                $diagnosisItems = is_array($diagRes->json('data')) ? $diagRes->json('data') : [];
                $diagnosisStats = is_array($diagRes->json('meta.stats_by_label'))
                    ? $diagRes->json('meta.stats_by_label')
                    : [];
            }
        } catch (\Throwable) {
            $apiError = 'Tidak dapat terhubung ke server Backend API (port 8000).';
        }

        $metrics = $this->computeMetrics($plants, $diagnosisItems, $diagnosisStats);

        return view('karyawan.tanaman.index', [
            'plants' => $plants,
            'diagnosisItems' => $diagnosisItems,
            'diagnosisStats' => $diagnosisStats,
            'metrics' => $metrics,
            'apiError' => $apiError,
        ]);
    }

    private function extractLubangCount(?string $keterangan): int
    {
        if (!is_string($keterangan) || $keterangan === '') {
            return 0;
        }
        if (preg_match('/(\d+)\s*lubang/i', $keterangan, $matches)) {
            return (int) $matches[1];
        }
        return 0;
    }

    private function computeMetrics(array $plants, array $diagnosisItems, array $diagnosisStats): array
    {
        $activePlants = array_values(array_filter(
            $plants,
            fn ($p) => in_array((string) ($p['status'] ?? ''), self::ACTIVE_STATUSES, true)
        ));
        $finishedPlants = array_values(array_filter(
            $plants,
            fn ($p) => !in_array((string) ($p['status'] ?? ''), self::ACTIVE_STATUSES, true)
        ));

        $totalLubang = 0;
        $estimatedTargetGrams = 0.0;
        $actualBiomassGrams = 0.0;
        $varietasSet = [];
        $nearestPlant = null;
        $minDays = null;
        $readyCount = 0;

        $phVals = [];
        $ecVals = [];
        $optimalBatches = 0;
        $measuredBatches = 0;

        foreach ($activePlants as $p) {
            $lubang = $this->extractLubangCount($p['keterangan'] ?? null);
            $totalLubang += $lubang;

            $varName = trim((string) ($p['varietas'] ?? ''));
            if ($varName !== '') {
                $varietasSet[$varName] = true;
            }

            $latest = is_array($p['latest_growth_record'] ?? null) ? $p['latest_growth_record'] : null;
            $berat = $latest && isset($latest['berat_gram']) && is_numeric($latest['berat_gram'])
                ? (float) $latest['berat_gram']
                : 0.0;
            $mult = $lubang > 0 ? $lubang : 1;
            if ($berat > 0) {
                $actualBiomassGrams += $berat * $mult;
            }
            $estimatedTargetGrams += max($berat, 170.0) * $mult;

            if (isset($p['hari_menuju_panen']) && is_numeric($p['hari_menuju_panen'])) {
                $days = (int) $p['hari_menuju_panen'];
                if ($days <= 0) {
                    $readyCount++;
                }
                if ($minDays === null || $days < $minDays) {
                    $minDays = $days;
                    $nearestPlant = $p;
                }
            }

            if ($latest) {
                $ph = isset($latest['ph_air']) && is_numeric($latest['ph_air']) ? (float) $latest['ph_air'] : null;
                $ec = isset($latest['ec']) && is_numeric($latest['ec']) ? (float) $latest['ec'] : null;
                if ($ph !== null) {
                    $phVals[] = $ph;
                }
                if ($ec !== null) {
                    $ecVals[] = $ec;
                }
                if ($ph !== null || $ec !== null) {
                    $measuredBatches++;
                    $phOk = $ph === null || ($ph >= 5.5 && $ph <= 6.5);
                    $ecOk = $ec === null || ($ec >= 0.8 && $ec <= 1.8);
                    if ($phOk && $ecOk) {
                        $optimalBatches++;
                    }
                }
            }
        }

        $estKg = $estimatedTargetGrams / 1000.0;
        $actKg = $actualBiomassGrams / 1000.0;
        $varietasList = array_keys($varietasSet);

        if ($readyCount > 0) {
            $batchSub1 = "{$readyCount} Batch Siap Panen";
        } elseif ($minDays !== null) {
            $batchSub1 = "Panen {$minDays} Hari Lagi";
        } else {
            $batchSub1 = 'Belum Ada Jadwal';
        }

        $totalDiag = count($diagnosisItems);
        if ($totalDiag > 0) {
            $healthyDiag = (int) ($diagnosisStats['Healthy'] ?? 0);
            $healthPct = round(($healthyDiag / $totalDiag) * 100, 1);
            $healthSub1 = $healthPct >= 80 ? 'Kondisi Prima' : 'Perlu Penanganan';
            $healthSub2 = "{$healthyDiag}/{$totalDiag} Diagnosis Sehat";
        } elseif ($measuredBatches > 0) {
            $healthPct = round(($optimalBatches / $measuredBatches) * 100, 1);
            $avgPh = count($phVals) > 0 ? number_format(array_sum($phVals) / count($phVals), 1) : '-';
            $avgEc = count($ecVals) > 0 ? number_format(array_sum($ecVals) / count($ecVals), 2) : '-';
            $healthSub1 = $optimalBatches === $measuredBatches ? 'Parameter Air Optimal' : 'Cek pH / EC Nutrisi';
            $healthSub2 = "pH {$avgPh} • EC {$avgEc} mS/cm";
        } else {
            $healthPct = 0.0;
            $healthSub1 = 'Belum Ada Pengamatan';
            $healthSub2 = 'Menunggu Catatan / SVM';
        }

        return [
            'active_count' => count($activePlants),
            'finished_count' => count($finishedPlants),
            'batch_sub1' => $batchSub1,
            'batch_sub2' => count($finishedPlants) . ' Batch Selesai',
            'populasi_value' => $totalLubang > 0 ? number_format($totalLubang, 0, ',', '.') : (string) count($activePlants),
            'populasi_unit' => $totalLubang > 0 ? 'Lubang' : 'Batch',
            'populasi_sub1' => count($activePlants) . ' Batch Beroperasi',
            'populasi_sub2' => count($varietasList) > 0 ? implode(', ', array_slice($varietasList, 0, 2)) : 'Belum ada varietas',
            'panen_value' => $estKg >= 1000 ? number_format($estKg / 1000.0, 2, '.', '') : number_format($estKg, 1, '.', ''),
            'panen_unit' => $estKg >= 1000 ? 'Ton' : 'kg',
            'panen_sub1' => $nearestPlant ? ($nearestPlant['kode_tanaman'] ?? '-') : 'Belum ada batch',
            'panen_sub2' => 'Terukur: ' . number_format($actKg, 2, '.', '') . ' kg',
            'health_value' => number_format($healthPct, 1, '.', ''),
            'health_sub1' => $healthSub1,
            'health_sub2' => $healthSub2,
            'varietas_list' => $varietasList,
            'active_plants' => $activePlants,
        ];
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
