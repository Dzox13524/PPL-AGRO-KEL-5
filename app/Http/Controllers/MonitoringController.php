<?php

namespace App\Http\Controllers;

use App\Services\BackendApi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\View\View;

class MonitoringController extends Controller
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

        $plantCharts = [];
        $growthLogs = [];

        foreach ($plants as $plant) {
            $pid = $plant['id'] ?? null;
            if (!is_numeric($pid)) {
                continue;
            }
            $plantId = (int) $pid;
            $records = [];
            $historyMeta = [];
            $chart = [];

            if ($apiError === null) {
                try {
                    $histRes = BackendApi::get("/plants/{$plantId}/growth-history", [], $request, $auth['token']);
                    if ($histRes->successful()) {
                        $records = is_array($histRes->json('data')) ? $histRes->json('data') : [];
                        $historyMeta = is_array($histRes->json('meta')) ? $histRes->json('meta') : [];
                    }

                    $chartRes = BackendApi::get("/plants/{$plantId}/growth-chart", [], $request, $auth['token']);
                    if ($chartRes->successful() && is_array($chartRes->json('data'))) {
                        $chart = $chartRes->json('data');
                    }
                } catch (\Throwable) {
                    // Lanjutkan memuat tanaman lain.
                }
            }

            $plantCharts[$plantId] = [
                'records' => $records,
                'meta' => $historyMeta,
                'chart' => $chart,
                'svg' => $this->buildSvgModel($records, $chart),
            ];

            foreach ($records as $rec) {
                $growthLogs[] = array_merge($rec, [
                    'kode_tanaman' => $plant['kode_tanaman'] ?? '-',
                    'nama_tanaman' => $plant['nama'] ?? 'Selada',
                    'varietas' => $plant['varietas'] ?? '-',
                    'plant_status' => $plant['status'] ?? 'pertumbuhan',
                    'keterangan_tanaman' => $plant['keterangan'] ?? '-',
                ]);
            }
        }

        usort($growthLogs, function ($a, $b) {
            $cmp = strcmp((string) ($b['tanggal_pengamatan'] ?? ''), (string) ($a['tanggal_pengamatan'] ?? ''));
            return $cmp !== 0 ? $cmp : ((int) ($b['id'] ?? 0) <=> (int) ($a['id'] ?? 0));
        });

        $firstPlant = $plants[0] ?? null;
        $firstPlantId = $firstPlant && isset($firstPlant['id']) ? (int) $firstPlant['id'] : null;
        $metrics = $this->computeSummary($plants, $growthLogs, $diagnosisItems, $diagnosisStats);

        return view('karyawan.monitoring.index', [
            'plants' => $plants,
            'plantCharts' => $plantCharts,
            'growthLogs' => $growthLogs,
            'firstPlant' => $firstPlant,
            'firstPlantId' => $firstPlantId,
            'metrics' => $metrics,
            'diagnosisItems' => $diagnosisItems,
            'diagnosisStats' => $diagnosisStats,
            'apiError' => $apiError,
        ]);
    }

    private function buildSvgModel(array $records, array $chart): array
    {
        $series = is_array($chart['series'] ?? null) ? $chart['series'] : [];
        $idealRaw = is_array($series['ideal_height_cm'] ?? null) ? $series['ideal_height_cm'] : [];
        $points = [];
        $allVals = [4.0];

        foreach ($records as $idx => $rec) {
            if (!isset($rec['tinggi_cm']) || !is_numeric($rec['tinggi_cm'])) {
                continue;
            }
            $hVal = (float) $rec['tinggi_cm'];
            $idealVal = isset($idealRaw[$idx]) && is_numeric($idealRaw[$idx]) ? (float) $idealRaw[$idx] : null;
            $allVals[] = $hVal;
            if ($idealVal !== null) {
                $allVals[] = $idealVal;
            }
            $points[] = [
                'hst' => (int) ($rec['umur_tanaman'] ?? 0),
                'tinggi_cm' => $hVal,
                'ideal_cm' => $idealVal,
                'berat_gram' => isset($rec['berat_gram']) && is_numeric($rec['berat_gram']) ? (float) $rec['berat_gram'] : null,
            ];
        }

        $maxVal = max($allVals);
        $step = $maxVal <= 8 ? 2 : 5;
        $maxY = max($step * 4, (int) (ceil($maxVal / $step) * $step));
        $yTicks = [
            ['y' => 40, 'label' => $maxY . ' cm'],
            ['y' => 95, 'label' => round($maxY * 0.75) . ' cm'],
            ['y' => 150, 'label' => round($maxY * 0.5) . ' cm'],
            ['y' => 205, 'label' => round($maxY * 0.25) . ' cm'],
            ['y' => 260, 'label' => '0'],
        ];

        $n = count($points);
        if ($n === 0) {
            return ['has_data' => false, 'y_ticks' => $yTicks, 'points' => [], 'actual_path' => '', 'ideal_path' => ''];
        }

        $xStart = 90.0;
        $xEnd = 820.0;
        $yTop = 40.0;
        $yBottom = 260.0;
        $coords = [];
        $idealCoords = [];

        foreach ($points as $i => $pt) {
            $x = $n === 1 ? ($xStart + $xEnd) / 2.0 : $xStart + ($i / ($n - 1)) * ($xEnd - $xStart);
            $y = $yBottom - (($pt['tinggi_cm'] / $maxY) * ($yBottom - $yTop));
            $coords[] = [
                'x' => round($x, 1),
                'y' => round($y, 1),
                'label' => 'H-' . $pt['hst'],
                'tinggi_cm' => $pt['tinggi_cm'],
                'is_last' => $i === $n - 1,
            ];
            if ($pt['ideal_cm'] !== null) {
                $iy = $yBottom - (($pt['ideal_cm'] / $maxY) * ($yBottom - $yTop));
                $idealCoords[] = ['x' => round($x, 1), 'y' => round($iy, 1)];
            }
        }

        $actualPath = '';
        foreach ($coords as $i => $c) {
            $actualPath .= ($i === 0 ? 'M ' : ' L ') . $c['x'] . ' ' . $c['y'];
        }

        $idealPath = '';
        foreach ($idealCoords as $i => $ic) {
            $idealPath .= ($i === 0 ? 'M ' : ' L ') . $ic['x'] . ' ' . $ic['y'];
        }

        return [
            'has_data' => true,
            'y_ticks' => $yTicks,
            'points' => $coords,
            'actual_path' => $actualPath,
            'ideal_path' => $idealPath,
        ];
    }

    private function computeSummary(array $plants, array $growthLogs, array $diagnosisItems, array $diagnosisStats): array
    {
        $activePlants = array_values(array_filter(
            $plants,
            fn ($p) => in_array((string) ($p['status'] ?? ''), self::ACTIVE_STATUSES, true)
        ));

        $totalLubang = 0;
        $estGrams = 0.0;
        $actGrams = 0.0;
        $minDays = null;
        $nearest = null;
        $readyCount = 0;
        $varietas = [];
        $phVals = [];
        $ecVals = [];
        $optimalWater = 0;
        $measuredWater = 0;

        foreach ($activePlants as $p) {
            $lubang = 0;
            if (preg_match('/(\d+)\s*lubang/i', (string) ($p['keterangan'] ?? ''), $m)) {
                $lubang = (int) $m[1];
                $totalLubang += $lubang;
            }
            if (!empty($p['varietas'])) {
                $varietas[$p['varietas']] = true;
            }
            $latest = is_array($p['latest_growth_record'] ?? null) ? $p['latest_growth_record'] : null;
            $berat = $latest && is_numeric($latest['berat_gram'] ?? null) ? (float) $latest['berat_gram'] : 0.0;
            $mult = $lubang > 0 ? $lubang : 1;
            if ($berat > 0) {
                $actGrams += $berat * $mult;
            }
            $estGrams += max($berat, 170.0) * $mult;

            if (is_numeric($p['hari_menuju_panen'] ?? null)) {
                $d = (int) $p['hari_menuju_panen'];
                if ($d <= 0) {
                    $readyCount++;
                }
                if ($minDays === null || $d < $minDays) {
                    $minDays = $d;
                    $nearest = $p;
                }
            }

            if ($latest) {
                $ph = is_numeric($latest['ph_air'] ?? null) ? (float) $latest['ph_air'] : null;
                $ec = is_numeric($latest['ec'] ?? null) ? (float) $latest['ec'] : null;
                if ($ph !== null) {
                    $phVals[] = $ph;
                }
                if ($ec !== null) {
                    $ecVals[] = $ec;
                }
                if ($ph !== null || $ec !== null) {
                    $measuredWater++;
                    if (($ph === null || ($ph >= 5.5 && $ph <= 6.5)) && ($ec === null || ($ec >= 0.8 && $ec <= 1.8))) {
                        $optimalWater++;
                    }
                }
            }
        }

        $totalDiag = count($diagnosisItems);
        if ($totalDiag > 0) {
            $healthy = (int) ($diagnosisStats['Healthy'] ?? 0);
            $healthPct = round(($healthy / $totalDiag) * 100, 1);
            $healthSub1 = $healthPct >= 80 ? 'Kondisi Prima' : 'Perlu Penanganan';
            $healthSub2 = "{$healthy}/{$totalDiag} Diagnosis Sehat";
        } elseif ($measuredWater > 0) {
            $healthPct = round(($optimalWater / $measuredWater) * 100, 1);
            $avgPh = count($phVals) ? number_format(array_sum($phVals) / count($phVals), 1) : '-';
            $avgEc = count($ecVals) ? number_format(array_sum($ecVals) / count($ecVals), 2) : '-';
            $healthSub1 = $optimalWater === $measuredWater ? 'Parameter Air Optimal' : 'Cek pH / EC Nutrisi';
            $healthSub2 = "pH {$avgPh} • EC {$avgEc} mS/cm";
        } else {
            $healthPct = 0.0;
            $healthSub1 = 'Belum Ada Data';
            $healthSub2 = 'Menunggu Pengukuran';
        }

        $estKg = $estGrams / 1000.0;
        $actKg = $actGrams / 1000.0;

        return [
            'active_count' => count($activePlants),
            'batch_sub1' => $readyCount > 0 ? "{$readyCount} Batch Siap Panen" : ($minDays !== null ? "Panen {$minDays} Hari Lagi" : 'Belum Ada Jadwal'),
            'batch_sub2' => count($growthLogs) . ' Log Tercatat',
            'populasi_value' => $totalLubang > 0 ? number_format($totalLubang, 0, ',', '.') : (string) count($activePlants),
            'populasi_unit' => $totalLubang > 0 ? 'Lubang' : 'Batch',
            'populasi_sub1' => count($activePlants) . ' Batch Beroperasi',
            'populasi_sub2' => count($varietas) ? implode(', ', array_slice(array_keys($varietas), 0, 2)) : '-',
            'panen_value' => $estKg >= 1000 ? number_format($estKg / 1000.0, 2, '.', '') : number_format($estKg, 1, '.', ''),
            'panen_unit' => $estKg >= 1000 ? 'Ton' : 'kg',
            'panen_sub1' => $nearest ? ($nearest['kode_tanaman'] ?? '-') : '-',
            'panen_sub2' => 'Terukur: ' . number_format($actKg, 2, '.', '') . ' kg',
            'health_value' => number_format($healthPct, 1, '.', ''),
            'health_sub1' => $healthSub1,
            'health_sub2' => $healthSub2,
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

