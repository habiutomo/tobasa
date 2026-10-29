<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use App\Models\Loket;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class DisplayController extends Controller
{
    public function index(): View
    {
        return view('display.index', [
            'namaPuskesmas' => Setting::get('nama_puskesmas', 'PUSKESMAS'),
            'marqueeText' => Setting::get('marquee_text', ''),
            'suaraAktif' => Setting::get('suara_aktif', '1') === '1',
            'volumeSuara' => (int) Setting::get('volume_suara', 100),
            'ulangSuara' => (int) Setting::get('ulang_suara', 1),
        ]);
    }

    public function data(): JsonResponse
    {
        $lokets = Loket::with('poli')->where('is_active', true)->orderBy('id')->get()->map(function (Loket $loket) {
            $calling = Antrian::whereDate('tanggal', today())
                ->where('status', 'calling')
                ->where('loket_id', $loket->id)
                ->with('poli')
                ->latest('called_at')
                ->first();

            $next = Antrian::whereDate('tanggal', today())
                ->where('status', 'waiting')
                ->whereHas('poli', function ($q) use ($loket) {
                    $q->where('id', $loket->poli_id);
                })
                ->orderBy('nomor')
                ->first();

            $antrian = $calling ?: $next;

            return [
                'id' => $loket->id,
                'nama' => $loket->nama,
                'poli' => $loket->poli?->nama,
                'warna' => $loket->poli?->warna ?? '#6c757d',
                'nomor_dipanggil' => $antrian?->no_antrian,
                'called_at' => $antrian?->called_at ? $antrian->called_at->format('Y-m-d H:i:s') : null,
                'is_calling' => (bool) $calling,
            ];
        });

        $lastServed = Antrian::whereDate('tanggal', today())
            ->whereIn('status', ['done', 'calling', 'skipped'])
            ->with('poli', 'loket')
            ->orderByDesc('updated_at')
            ->take(10)
            ->get()
            ->map(function (Antrian $antrian) {
                return [
                    'no_antrian' => $antrian->no_antrian,
                    'nama_poli' => $antrian->poli?->nama,
                    'loket' => $antrian->loket?->nama,
                    'status' => $antrian->status,
                ];
            });

        return response()->json([
            'lokets' => $lokets,
            'last_served' => $lastServed,
            'waktu' => now()->format('H:i:s'),
            'suara_aktif' => Setting::get('suara_aktif', '1') === '1',
            'volume_suara' => (int) Setting::get('volume_suara', 100),
            'ulang_suara' => (int) Setting::get('ulang_suara', 1),
        ]);
    }
}