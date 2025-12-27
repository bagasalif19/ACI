<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Ambil user + token dari session
        $user  = Session::get('user');
        $token = Session::get('auth_token');

        if (empty($user) || empty($token)) {
            return redirect()->route('login')->withErrors(['_global' => 'Silakan login terlebih dahulu.']);
        }

        $baseUrl = rtrim(env('API_BASE_URL', ''), '/');
        if (empty($baseUrl)) {
            return redirect()->route('login')->withErrors(['_global' => 'API_BASE_URL belum diset di .env']);
        }

        // Normalisasi user ke array
        $userArr = is_object($user) ? (array) $user : (array) $user;

        // =========================
        // 1) REKAP PER STATUS
        // =========================
        $rekapStatus = [];
        $totalLaporan = 0;

        try {
            $respStatus = Http::withToken($token)->get($baseUrl . '/api/rekap');
            if ($respStatus->status() === 401) {
                return redirect()->route('login')->withErrors(['_global' => 'Token expired. Silakan login ulang.']);
            }
            if ($respStatus->ok()) {
                $json = $respStatus->json() ?? [];
                $rekapStatus = $json['rekap_status'] ?? [];
                $totalLaporan = (int)($json['total'] ?? array_sum($rekapStatus));
            }
        } catch (\Throwable $e) {
            // biarkan kosong; nanti view tampilkan pesan fallback
        }

        // =========================
        // 2) COUNT PER KECAMATAN
        //    - ambil list kecamatan dari endpoint datatables
        //    - lalu ambil total laporan per kecamatan via /api/rekap/kecamatan?kecamatan_id=...
        // =========================
        $kecamatanCounts = [];   // untuk tabel (nama => total)
        $chartLabels = [];       // untuk chart
        $chartValues = [];

        try {
            // ambil semua kecamatan (paksa panjang besar biar tidak kepotong)
            $respKec = Http::withToken($token)->get($baseUrl . '/api/wilayah-kecamatan/datatables', [
                'draw' => 1,
                'start' => 0,
                'length' => 10000,
                'search' => ['value' => ''],
            ]);

            if ($respKec->status() === 401) {
                return redirect()->route('login')->withErrors(['_global' => 'Token expired. Silakan login ulang.']);
            }

            $rows = [];
            if ($respKec->ok()) {
                $kjson = $respKec->json() ?? [];
                $rows = $kjson['data'] ?? [];
            }

            // helper kecil untuk ambil field fleksibel
            $pick = function(array $row, array $keys) {
                foreach ($keys as $k) {
                    if (array_key_exists($k, $row) && $row[$k] !== null && $row[$k] !== '') {
                        return $row[$k];
                    }
                }
                return null;
            };

            // siapkan daftar kecamatan (id + nama)
            $kecamatanList = [];
            foreach ($rows as $r) {
                if (!is_array($r)) continue;
                $id = $pick($r, ['id', 'kecamatan_id', 'kode', 'kode_kecamatan']);
                $nm = $pick($r, ['nama_kecamatan', 'nama', 'name', 'kecamatan']);
                if ($id !== null && $nm !== null) {
                    $kecamatanList[] = ['id' => $id, 'nama' => $nm];
                }
            }

            // kalau tidak ada data kecamatan, stop
            if (!empty($kecamatanList)) {

                // request rekap per kecamatan (pakai pool biar lebih cepat)
                $responses = Http::pool(function ($pool) use ($kecamatanList, $baseUrl, $token) {
                    $reqs = [];
                    foreach ($kecamatanList as $kec) {
                        $reqs[$kec['id']] = $pool->withToken($token)->get($baseUrl . '/api/rekap/kecamatan', [
                            'kecamatan_id' => $kec['id'],
                        ]);
                    }
                    return $reqs;
                });

                // susun hasil (nama => total)
                foreach ($kecamatanList as $kec) {
                    $id = $kec['id'];
                    $nama = $kec['nama'];

                    $r = $responses[$id] ?? null;
                    if ($r && $r->ok()) {
                        $j = $r->json() ?? [];
                        $total = (int)($j['total'] ?? 0);
                        $kecamatanCounts[] = [
                            'id' => $id,
                            'nama' => $nama,
                            'total' => $total
                        ];
                    } else {
                        $kecamatanCounts[] = [
                            'id' => $id,
                            'nama' => $nama,
                            'total' => 0
                        ];
                    }
                }

                // urutkan desc
                usort($kecamatanCounts, fn($a, $b) => $b['total'] <=> $a['total']);

                // chart: ambil TOP 10 biar rapih
                $top = array_slice($kecamatanCounts, 0, 10);
                foreach ($top as $t) {
                    $chartLabels[] = $t['nama'];
                    $chartValues[] = (int)$t['total'];
                }
            }
        } catch (\Throwable $e) {
            // fallback: kosong
        }

        return view('backend.dashboard.index', [
            'user' => $userArr,

            // status
            'rekapStatus' => $rekapStatus,
            'totalLaporan' => $totalLaporan,

            // kecamatan
            'kecamatanCounts' => $kecamatanCounts,
            'chartLabels' => $chartLabels,
            'chartValues' => $chartValues,
        ]);
    }
}
