<?php

namespace App\Http\Controllers;

use App\Services\BackendApi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\View\View;

class StockController extends Controller
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

        $overview = [];
        $plants = [];
        $stocks = [];
        $stockSummary = [];
        $movements = [];
        $apiError = null;

        try {
            $overviewRes = BackendApi::get('/stocks/planted-overview', [], $request, $auth['token']);
            if ($overviewRes->status() === 401) {
                return $this->redirectUnauthorized($request);
            }
            if ($overviewRes->successful() && is_array($overviewRes->json('data'))) {
                $overview = $overviewRes->json('data');
                $plants = is_array($overview['plants'] ?? null) ? $overview['plants'] : [];
                $stockSummary = is_array($overview['stock_summary'] ?? null) ? $overview['stock_summary'] : [];
            }

            if (count($plants) === 0) {
                $plantsRes = BackendApi::get('/plants', ['per_page' => 50], $request, $auth['token']);
                if ($plantsRes->successful() && is_array($plantsRes->json('data'))) {
                    $plants = $plantsRes->json('data');
                }
            }

            $stocksRes = BackendApi::get('/stocks', ['per_page' => 100], $request, $auth['token']);
            if ($stocksRes->successful() && is_array($stocksRes->json('data'))) {
                $stocks = $stocksRes->json('data');
                if (empty($stockSummary) && is_array($stocksRes->json('meta.summary'))) {
                    $stockSummary = $stocksRes->json('meta.summary');
                }
            }

            $movRes = BackendApi::get('/stocks/movements', ['limit' => 30], $request, $auth['token']);
            if ($movRes->successful() && is_array($movRes->json('data'))) {
                $movements = $movRes->json('data');
            }
        } catch (\Throwable) {
            $apiError = 'Tidak dapat terhubung ke server Backend API (port 8000).';
        }

        $metrics = $this->computeMetrics($plants, $overview, $stocks, $stockSummary);

        return view('admin.stok.index', [
            'overview' => $overview,
            'plants' => $plants,
            'stocks' => $stocks,
            'stockSummary' => $stockSummary,
            'movements' => $movements,
            'metrics' => $metrics,
            'apiError' => $apiError,
        ]);
    }

    private function computeMetrics(array $plants, array $overview, array $stocks, array $stockSummary): array
    {
        $totalLubang = 0;
        foreach ($plants as $p) {
            $ket = (string) ($p['keterangan'] ?? '');
            if (preg_match('/(\d+)\s*lubang/i', $ket, $m)) {
                $totalLubang += (int) $m[1];
            }
        }

        $varietasCount = count(is_array($overview['by_varietas'] ?? null) ? $overview['by_varietas'] : []);
        $bibitItems = array_values(array_filter($stocks, fn ($s) => ($s['jenis'] ?? '') === 'bibit'));
        $totalBibit = array_sum(array_map(fn ($s) => (float) ($s['jumlah_stok'] ?? 0), $bibitItems));
        $lowBibitCount = count(array_filter($bibitItems, fn ($s) => !empty($s['is_low_stock'])));

        $nutrisiItems = array_values(array_filter($stocks, fn ($s) => ($s['jenis'] ?? '') === 'nutrisi'));
        usort($nutrisiItems, function ($a, $b) {
            $aAb = stripos((string) ($a['nama_barang'] ?? ''), 'ab mix') !== false ? 0 : 1;
            $bAb = stripos((string) ($b['nama_barang'] ?? ''), 'ab mix') !== false ? 0 : 1;
            return $aAb <=> $bAb;
        });
        $primaryNut = $nutrisiItems[0] ?? null;
        $secondaryNut = $nutrisiItems[1] ?? null;
        $lainnyaCount = count(array_filter($stocks, fn ($s) => ($s['jenis'] ?? '') === 'lainnya'));

        $nutValue = $primaryNut ? $this->formatQty((float) ($primaryNut['jumlah_stok'] ?? 0)) : '0';
        $nutUnit = $primaryNut ? (($primaryNut['satuan'] ?? 'unit') . ' ' . ($primaryNut['nama_barang'] ?? 'Nutrisi')) : 'Unit';
        $nutSubParts = [];
        if ($secondaryNut) {
            $shortName = trim(explode('(', (string) ($secondaryNut['nama_barang'] ?? 'Nutrisi'))[0]);
            $nutSubParts[] = $shortName . ': ' . $this->formatQty((float) ($secondaryNut['jumlah_stok'] ?? 0)) . ' ' . ($secondaryNut['satuan'] ?? '');
        }
        $nutSubParts[] = "{$lainnyaCount} item media/lainnya";

        return [
            'tanaman_value' => $totalLubang > 0 ? number_format($totalLubang, 0, ',', '.') : (string) count($plants),
            'tanaman_unit' => $totalLubang > 0 ? 'Lubang Tanam' : 'Batch Aktif',
            'tanaman_sub' => count($plants) . ' Batch aktif' . ($varietasCount > 0 ? " ({$varietasCount} varietas)" : ''),
            'benih_value' => $this->formatQty($totalBibit),
            'benih_unit' => count($bibitItems) > 0 ? ($bibitItems[0]['satuan'] ?? 'biji') : 'biji',
            'benih_sub' => count($bibitItems) . ' jenis bibit' . ($lowBibitCount > 0 ? " • {$lowBibitCount} stok menipis" : ' • stok aman'),
            'nutrisi_value' => $nutValue,
            'nutrisi_unit' => $nutUnit,
            'nutrisi_sub' => implode(' • ', $nutSubParts),
            'low_stock_count' => (int) ($stockSummary['low_stock_items'] ?? 0),
        ];
    }

    private function formatQty(float $val): string
    {
        $str = number_format($val, 2, ',', '.');
        return rtrim(rtrim($str, '0'), ',');
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
