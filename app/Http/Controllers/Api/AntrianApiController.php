<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Antrian;
use App\Models\Loket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AntrianApiController extends Controller
{
    private function loket(Request $request): Loket
    {
        $loket = $request->attributes->get('api_loket');

        if (! $loket instanceof Loket) {
            abort(403, 'Loket tidak terkonfigurasi. Hubungi administrator.');
        }

        return $loket;
    }

    /**
     * Status terbaru: nomor yang sedang dipanggil + daftar antrian menunggu.
     */
    public function status(Request $request): JsonResponse
    {
        $loket = $this->loket($request);

        $calling = Antrian::whereDate('tanggal', today())
            ->where('status', 'calling')
            ->where('loket_id', $loket->id)
            ->latest('called_at')
            ->first();

        $waiting = Antrian::whereDate('tanggal', today())
            ->where('status', 'waiting')
            ->where('poli_id', $loket->poli_id)
            ->orderBy('nomor')
            ->get()
            ->map(fn (Antrian $a) => [
                'id' => $a->id,
                'no_antrian' => $a->no_antrian,
                'nama_pasien' => $a->nama_pasien,
            ]);

        return response()->json([
            'success' => true,
            'loket' => $loket->nama,
            'poli' => $loket->poli?->nama,
            'current' => $calling ? [
                'id' => $calling->id,
                'no_antrian' => $calling->no_antrian,
                'nama_pasien' => $calling->nama_pasien,
                'called_at' => $calling->called_at?->format('H:i:s'),
            ] : null,
            'waiting' => $waiting,
            'waiting_count' => $waiting->count(),
            'waktu' => now()->format('H:i:s'),
        ]);
    }

    /**
     * Memanggil nomor antrian berikutnya.
     */
    public function panggil(Request $request): JsonResponse
    {
        $loket = $this->loket($request);
        $now = now();

        $next = Antrian::whereDate('tanggal', today())
            ->where('status', 'waiting')
            ->where('poli_id', $loket->poli_id)
            ->orderBy('nomor')
            ->first();

        if (! $next) {
            return response()->json(['success' => false, 'message' => 'Tidak ada antrian yang menunggu.'], 404);
        }

        Antrian::whereDate('tanggal', today())
            ->where('status', 'calling')
            ->where('loket_id', $loket->id)
            ->update(['status' => 'done', 'finished_at' => $now]);

        $next->update([
            'status' => 'calling',
            'loket_id' => $loket->id,
            'called_at' => $now,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Memanggil '.$next->no_antrian,
            'antrian' => [
                'id' => $next->id,
                'no_antrian' => $next->no_antrian,
                'nama_pasien' => $next->nama_pasien,
                'nama_poli' => $next->poli->nama,
                'loket' => $loket->nama,
                'called_at' => $now->format('H:i:s'),
            ],
        ]);
    }

    /**
     * Memanggil ulang nomor yang sedang dipanggil.
     */
    public function panggilUlang(Request $request): JsonResponse
    {
        $loket = $this->loket($request);

        $calling = Antrian::whereDate('tanggal', today())
            ->where('status', 'calling')
            ->where('loket_id', $loket->id)
            ->latest('called_at')
            ->first();

        if (! $calling) {
            return response()->json(['success' => false, 'message' => 'Tidak ada nomor yang sedang dipanggil.'], 404);
        }

        $calling->update([
            'called_at' => now(),
            'finished_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Memanggil ulang '.$calling->no_antrian,
            'antrian' => [
                'id' => $calling->id,
                'no_antrian' => $calling->no_antrian,
                'nama_pasien' => $calling->nama_pasien,
                'nama_poli' => $calling->poli->nama,
                'loket' => $loket->nama,
            ],
        ]);
    }

    /**
     * Menandai pasien selesai dilayani.
     */
    public function selesai(Request $request): JsonResponse
    {
        $loket = $this->loket($request);
        $now = now();

        $calling = Antrian::whereDate('tanggal', today())
            ->where('status', 'calling')
            ->where('loket_id', $loket->id)
            ->latest('called_at')
            ->first();

        if (! $calling) {
            return response()->json(['success' => false, 'message' => 'Tidak ada nomor yang sedang dipanggil.'], 404);
        }

        $calling->update([
            'status' => 'done',
            'finished_at' => $now,
        ]);

        return response()->json([
            'success' => true,
            'message' => $calling->no_antrian.' selesai dilayani.',
        ]);
    }

    /**
     * Melewati nomor sedang dipanggil lalu beralih ke antrian berikutnya.
     */
    public function lewati(Request $request): JsonResponse
    {
        $loket = $this->loket($request);

        $calling = Antrian::whereDate('tanggal', today())
            ->where('status', 'calling')
            ->where('loket_id', $loket->id)
            ->latest('called_at')
            ->first();

        if (! $calling) {
            return response()->json(['success' => false, 'message' => 'Tidak ada nomor yang sedang dipanggil.'], 404);
        }

        $calling->update([
            'status' => 'skipped',
            'skipped_at' => now(),
            'finished_at' => now(),
        ]);

        $now = now();

        $next = Antrian::whereDate('tanggal', today())
            ->where('status', 'waiting')
            ->where('poli_id', $loket->poli_id)
            ->orderBy('nomor')
            ->first();

        if ($next) {
            $next->update([
                'status' => 'calling',
                'loket_id' => $loket->id,
                'called_at' => $now,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lewati '.$calling->no_antrian.'. Memanggil '.$next->no_antrian,
                'antrian' => [
                    'id' => $next->id,
                    'no_antrian' => $next->no_antrian,
                    'nama_pasien' => $next->nama_pasien,
                    'nama_poli' => $next->poli->nama,
                    'loket' => $loket->nama,
                ],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lewati '.$calling->no_antrian.'. Antrian kosong.',
        ]);
    }
}
