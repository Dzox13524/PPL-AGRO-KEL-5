<?php

namespace App\Http\Controllers;

use App\Services\BackendApi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\View\View;

class DashboardController extends Controller
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

        $user = $this->resolveUserFallback($request, $auth['token']);
        $apiError = null;

        try {
            $meResponse = BackendApi::get('/auth/me', [], $request, $auth['token']);
            if ($meResponse->status() === 401) {
                return $this->redirectUnauthorized($request);
            }
            if ($meResponse->successful() && is_array($meResponse->json('data'))) {
                $user = array_merge($user, $meResponse->json('data'));
                if (($user['role'] ?? $auth['role']) !== 'owner') {
                    return redirect()->route('karyawan.tanaman');
                }
            }
        } catch (\Throwable) {
            $apiError = 'Tidak dapat terhubung ke server Backend API untuk memuat data terbaru.';
        }

        $overview = [];
        $plants = [];
        $stockSummary = [];
        $stocks = [];
        $diagnosisItems = [];
        $diagnosisStats = [];

        if ($apiError === null) {
            try {
                $overviewRes = BackendApi::get('/stocks/planted-overview', [], $request, $auth['token']);
                if ($overviewRes->successful() && is_array($overviewRes->json('data'))) {
                    $overview = $overviewRes->json('data');
                    $plants = is_array($overview['plants'] ?? null) ? $overview['plants'] : [];
                    $stockSummary = is_array($overview['stock_summary'] ?? null) ? $overview['stock_summary'] : [];
                }

                // Jika tidak ada tanaman aktif, muat daftar seluruh tanaman (termasuk dipanen).
                if (count($plants) === 0) {
                    $allPlantsRes = BackendApi::get('/plants', ['per_page' => 20], $request, $auth['token']);
                    if ($allPlantsRes->successful() && is_array($allPlantsRes->json('data'))) {
                        $plants = $allPlantsRes->json('data');
                    }
                }

                $stocksRes = BackendApi::get('/stocks', ['per_page' => 100], $request, $auth['token']);
                if ($stocksRes->successful()) {
                    $stocks = is_array($stocksRes->json('data')) ? $stocksRes->json('data') : [];
                    if (empty($stockSummary) && is_array($stocksRes->json('meta.summary'))) {
                        $stockSummary = $stocksRes->json('meta.summary');
                    }
                }

                $diagRes = BackendApi::get('/diagnosis/history', ['per_page' => 50], $request, $auth['token']);
                if ($diagRes->successful()) {
                    $diagnosisItems = is_array($diagRes->json('data')) ? $diagRes->json('data') : [];
                    $diagnosisStats = is_array($diagRes->json('meta.stats_by_label'))
                        ? $diagRes->json('meta.stats_by_label')
                        : [];
                }
            } catch (\Throwable) {
                $apiError = 'Sebagian data dashboard gagal dimuat dari Backend API.';
            }
        }

        $plantDetails = [];
        foreach ($plants as $plant) {
            $plantId = $plant['id'] ?? null;
            if (!is_numeric($plantId)) {
                continue;
            }
            $pid = (int) $plantId;
            $records = [];
            $historyMeta = [];
            $chart = [];

            if ($apiError === null) {
                try {
                    $histRes = BackendApi::get("/plants/{$pid}/growth-history", [], $request, $auth['token']);
                    if ($histRes->successful()) {
                        $records = is_array($histRes->json('data')) ? $histRes->json('data') : [];
                        $historyMeta = is_array($histRes->json('meta')) ? $histRes->json('meta') : [];
                    }

                    $chartRes = BackendApi::get("/plants/{$pid}/growth-chart", [], $request, $auth['token']);
                    if ($chartRes->successful() && is_array($chartRes->json('data'))) {
                        $chart = $chartRes->json('data');
                    }
                } catch (\Throwable) {
                    // Tetap lanjutkan agar data tanaman/kartu lain tetap tampil.
                }
            }

            $plantDetails[$pid] = [
                'records' => $records,
                'meta' => $historyMeta,
                'chart' => $chart,
                'svg' => $this->buildSvgChartModel($chart),
            ];
        }

        $firstPlant = $plants[0] ?? null;
        $firstPlantId = $firstPlant && isset($firstPlant['id']) ? (int) $firstPlant['id'] : null;

        $summaryCards = $this->buildSummaryCards(
            $plants,
            $overview,
            $stocks,
            $stockSummary,
            $diagnosisItems,
            $diagnosisStats
        );

        return view('admin.dashboard.index', [
            'user' => $user,
            'overview' => $overview,
            'plants' => $plants,
            'plantDetails' => $plantDetails,
            'firstPlant' => $firstPlant,
            'firstPlantId' => $firstPlantId,
            'stocks' => $stocks,
            'stockSummary' => $stockSummary,
            'diagnosisItems' => $diagnosisItems,
            'diagnosisStats' => $diagnosisStats,
            'summaryCards' => $summaryCards,
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

    private function resolveUserFallback(Request $request, string $token): array
    {
        $user = [
            'username' => 'owner',
            'nama' => 'Pemilik Mitra',
            'role' => 'owner',
        ];

        $rawCookie = $request->cookie('hidara_user');
        if (is_string($rawCookie) && $rawCookie !== '') {
            $decoded = json_decode($rawCookie, true);
            if (is_array($decoded)) {
                $user = array_merge($user, array_filter($decoded, fn ($v) => $v !== null && $v !== ''));
            }
        }

        $parts = explode('.', $token);
        if (count($parts) === 3) {
            $decoded = base64_decode(strtr($parts[1], '-_', '+/'), true);
            $claims = is_string($decoded) ? json_decode($decoded, true) : null;
            if (is_array($claims)) {
                foreach (['username', 'nama', 'role'] as $field) {
                    if (!empty($claims[$field]) && is_string($claims[$field])) {
                        $user[$field] = $claims[$field];
                    }
                }
            }
        }

        return $user;
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

    private function formatQty(float $value): string
    {
        $formatted = number_format($value, 2, ',', '.');
        return rtrim(rtrim($formatted, '0'), ',');
    }

    private function buildSvgChartModel(array $chart): array
    {
        $labels = is_array($chart['labels'] ?? null) ? $chart['labels'] : [];
        $series = is_array($chart['series'] ?? null) ? $chart['series'] : [];
        $actualRaw = is_array($series['tinggi_cm'] ?? null) ? $series['tinggi_cm'] : [];
        $idealRaw = is_array($series['ideal_height_cm'] ?? null) ? $series['ideal_height_cm'] : [];

        $points = [];
        $allVals = [1.0];
        $count = min(count($labels), count($actualRaw));
        for ($i = 0; $i < $count; $i++) {
            $val = $actualRaw[$i];
            if ($val === null || !is_numeric($val)) {
                continue;
            }
            $idealVal = isset($idealRaw[$i]) && is_numeric($idealRaw[$i]) ? (float) $idealRaw[$i] : null;
            $fVal = (float) $val;
            $allVals[] = $fVal;
            if ($idealVal !== null) {
                $allVals[] = $idealVal;
            }
            $points[] = [
                'label' => (string) $labels[$i],
                'actual' => $fVal,
                'ideal' => $idealVal,
            ];
        }

        $maxVal = max($allVals);
        $step = $maxVal <= 8 ? 2 : 5;
        $maxY = max($step * 4, (int) (ceil($maxVal / $step) * $step));
        $yTicks = [
            ['y' => 30, 'label' => (string) $maxY],
            ['y' => 70, 'label' => (string) round($maxY * 0.75)],
            ['y' => 110, 'label' => (string) round($maxY * 0.5)],
            ['y' => 150, 'label' => (string) round($maxY * 0.25)],
            ['y' => 190, 'label' => '0'],
        ];

        $n = count($points);
        if ($n === 0) {
            return [
                'has_data' => false,
                'y_ticks' => $yTicks,
                'points' => [],
                'line_path' => '',
                'area_path' => '',
                'ideal_path' => '',
            ];
        }

        $xStart = 75.0;
        $xEnd = 540.0;
        $yTop = 30.0;
        $yBottom = 190.0;

        $plotted = [];
        $idealCoords = [];
        foreach ($points as $idx => $pt) {
            $x = $n === 1 ? ($xStart + $xEnd) / 2.0 : $xStart + ($idx / ($n - 1)) * ($xEnd - $xStart);
            $y = $yBottom - (($pt['actual'] / $maxY) * ($yBottom - $yTop));
            $plotted[] = [
                'x' => round($x, 1),
                'y' => round($y, 1),
                'label' => $pt['label'],
                'actual' => $pt['actual'],
                'is_last' => $idx === $n - 1,
            ];
            if ($pt['ideal'] !== null) {
                $iy = $yBottom - (($pt['ideal'] / $maxY) * ($yBottom - $yTop));
                $idealCoords[] = ['x' => round($x, 1), 'y' => round($iy, 1)];
            }
        }

        $linePath = '';
        foreach ($plotted as $idx => $p) {
            $linePath .= ($idx === 0 ? 'M ' : ' L ') . $p['x'] . ' ' . $p['y'];
        }
        $firstX = $plotted[0]['x'];
        $lastX = $plotted[$n - 1]['x'];
        $areaPath = $linePath . " L {$lastX} 190 L {$firstX} 190 Z";

        $idealPath = '';
        foreach ($idealCoords as $idx => $ip) {
            $idealPath .= ($idx === 0 ? 'M ' : ' L ') . $ip['x'] . ' ' . $ip['y'];
        }

        return [
            'has_data' => true,
            'y_ticks' => $yTicks,
            'points' => $plotted,
            'line_path' => $linePath,
            'area_path' => $areaPath,
            'ideal_path' => $idealPath,
        ];
    }

    private function buildSummaryCards(
        array $plants,
        array $overview,
        array $stocks,
        array $stockSummary,
        array $diagnosisItems,
        array $diagnosisStats
    ): array {
        $totalBatches = count($plants);
        $totalLubang = 0;
        $actualBiomassGrams = 0.0;
        $estimatedTargetGrams = 0.0;
        $harvestDays = [];
        $readyCount = 0;
        $phValues = [];
        $ecValues = [];
        $optimalWaterBatches = 0;
        $measuredWaterBatches = 0;

        foreach ($plants as $plant) {
            $lubang = $this->extractLubangCount($plant['keterangan'] ?? null);
            $totalLubang += $lubang;

            $latest = is_array($plant['latest_growth'] ?? null) ? $plant['latest_growth'] : null;
            $berat = $latest && isset($latest['berat_gram']) && is_numeric($latest['berat_gram'])
                ? (float) $latest['berat_gram']
                : 0.0;
            $multiplier = $lubang > 0 ? $lubang : 1;

            if ($berat > 0) {
                $actualBiomassGrams += $berat * $multiplier;
            }
            $targetPerPlant = max($berat, 170.0);
            $estimatedTargetGrams += $targetPerPlant * $multiplier;

            if (isset($plant['hari_menuju_panen']) && is_numeric($plant['hari_menuju_panen'])) {
                $days = (int) $plant['hari_menuju_panen'];
                $harvestDays[] = $days;
                if ($days <= 0) {
                    $readyCount++;
                }
            }

            if ($latest) {
                $ph = isset($latest['ph_air']) && is_numeric($latest['ph_air']) ? (float) $latest['ph_air'] : null;
                $ec = isset($latest['ec']) && is_numeric($latest['ec']) ? (float) $latest['ec'] : null;
                if ($ph !== null) {
                    $phValues[] = $ph;
                }
                if ($ec !== null) {
                    $ecValues[] = $ec;
                }
                if ($ph !== null || $ec !== null) {
                    $measuredWaterBatches++;
                    $phOk = $ph === null || ($ph >= 5.5 && $ph <= 6.5);
                    $ecOk = $ec === null || ($ec >= 0.8 && $ec <= 1.8);
                    if ($phOk && $ecOk) {
                        $optimalWaterBatches++;
                    }
                }
            }
        }

        $estimatedKg = $estimatedTargetGrams / 1000.0;
        $actualKg = $actualBiomassGrams / 1000.0;
        $minDays = count($harvestDays) > 0 ? min($harvestDays) : null;

        if ($readyCount > 0) {
            $harvestSub1 = "{$readyCount} Batch Siap Panen";
        } elseif ($minDays !== null) {
            $harvestSub1 = "Panen {$minDays} Hari Lagi";
        } else {
            $harvestSub1 = 'Belum Ada Jadwal';
        }

        // Card 2: Tanaman Aktif
        $varietasList = array_keys(is_array($overview['by_varietas'] ?? null) ? $overview['by_varietas'] : []);
        $varietasText = count($varietasList) > 0
            ? implode(', ', array_slice($varietasList, 0, 2))
            : 'Belum ada varietas';

        // Card 3: Ketersediaan Nutrisi & Stok
        $nutrisiItems = array_values(array_filter($stocks, fn ($s) => ($s['jenis'] ?? '') === 'nutrisi'));
        usort($nutrisiItems, function ($a, $b) {
            $aAb = stripos((string) ($a['nama_barang'] ?? ''), 'ab mix') !== false ? 0 : 1;
            $bAb = stripos((string) ($b['nama_barang'] ?? ''), 'ab mix') !== false ? 0 : 1;
            return $aAb <=> $bAb;
        });

        $bibitItems = array_values(array_filter($stocks, fn ($s) => ($s['jenis'] ?? '') === 'bibit'));
        $primaryNutrisi = $nutrisiItems[0] ?? null;
        $secondaryNutrisi = $nutrisiItems[1] ?? null;
        $totalBibitQty = array_sum(array_map(fn ($s) => (float) ($s['jumlah_stok'] ?? 0), $bibitItems));
        $lowStockCount = (int) ($stockSummary['low_stock_items'] ?? 0);

        $nutrientValue = $primaryNutrisi
            ? $this->formatQty((float) ($primaryNutrisi['jumlah_stok'] ?? 0))
            : '0';
        $nutrientUnit = $primaryNutrisi
            ? (($primaryNutrisi['satuan'] ?? 'unit') . ' ' . ($primaryNutrisi['nama_barang'] ?? 'Nutrisi'))
            : 'Item Nutrisi';
        $nutrientSub1 = $lowStockCount > 0 ? "{$lowStockCount} Stok di Bawah Min" : 'Semua Stok Aman';
        $nutrientSub2Parts = [];
        if ($secondaryNutrisi) {
            $secName = explode('(', (string) ($secondaryNutrisi['nama_barang'] ?? 'Nutrisi'))[0];
            $secQty = $this->formatQty((float) ($secondaryNutrisi['jumlah_stok'] ?? 0));
            $nutrientSub2Parts[] = trim($secName) . ': ' . $secQty . ' ' . ($secondaryNutrisi['satuan'] ?? '');
        }
        $nutrientSub2Parts[] = 'Bibit: ' . number_format($totalBibitQty, 0, ',', '.') . ' biji';

        // Card 4: Kesehatan Tanaman
        $totalDiag = count($diagnosisItems);
        if ($totalDiag > 0) {
            $healthyDiag = (int) ($diagnosisStats['Healthy'] ?? 0);
            $healthPct = round(($healthyDiag / $totalDiag) * 100, 1);
            $healthSub1 = $healthPct >= 80 ? 'Kondisi Prima' : 'Perlu Penanganan';
            $healthSub2 = "{$healthyDiag}/{$totalDiag} Diagnosis Sehat";
        } elseif ($measuredWaterBatches > 0) {
            $healthPct = round(($optimalWaterBatches / $measuredWaterBatches) * 100, 1);
            $avgPh = count($phValues) > 0 ? number_format(array_sum($phValues) / count($phValues), 1) : '-';
            $avgEc = count($ecValues) > 0 ? number_format(array_sum($ecValues) / count($ecValues), 2) : '-';
            $healthSub1 = $optimalWaterBatches === $measuredWaterBatches ? 'Parameter Air Optimal' : 'Cek pH / EC Nutrisi';
            $healthSub2 = "pH {$avgPh} • EC {$avgEc} mS/cm";
        } else {
            $healthPct = 0.0;
            $healthSub1 = 'Belum Ada Pengamatan';
            $healthSub2 = 'Menunggu Log / Diagnosis';
        }

        return [
            'harvest' => [
                'value' => $estimatedKg >= 1000
                    ? number_format($estimatedKg / 1000.0, 2, '.', '')
                    : number_format($estimatedKg, 1, '.', ''),
                'unit' => $estimatedKg >= 1000 ? 'Ton' : 'kg',
                'sub1' => $harvestSub1,
                'sub2' => 'Terukur: ' . number_format($actualKg, 2, '.', '') . ' kg',
            ],
            'active' => [
                'value' => $totalLubang > 0 ? number_format($totalLubang, 0, ',', '.') : (string) $totalBatches,
                'unit' => $totalLubang > 0 ? 'Lubang' : 'Batch',
                'sub1' => "{$totalBatches} Batch Aktif",
                'sub2' => $varietasText,
            ],
            'nutrient' => [
                'value' => $nutrientValue,
                'unit' => $nutrientUnit,
                'sub1' => $nutrientSub1,
                'sub1_warn' => $lowStockCount > 0,
                'sub2' => implode(' • ', $nutrientSub2Parts),
            ],
            'health' => [
                'value' => number_format($healthPct, 1, '.', ''),
                'unit' => '%',
                'sub1' => $healthSub1,
                'sub2' => $healthSub2,
            ],
        ];
    }
}
