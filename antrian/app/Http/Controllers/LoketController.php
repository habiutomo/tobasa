<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use App\Models\Loket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoketController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user?->isLoket(), 403);

        $availableLokets = $user->canAccessAllClusters()
            ? Loket::where('is_active', true)->whereNotNull('poli_id')->with('poli')->orderBy('nama')->get()
            : $user->loket()->where('is_active', true)->whereNotNull('poli_id')->with('poli')->get();

        $loket = $this->resolveLoket($request);

        return view('loket.index', [
            'loket' => $loket,
            'availableLokets' => $availableLokets,
            'allClusters' => $user->canAccessAllClusters(),
        ]);
    }

    public function pilih(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user?->isLoket(), 403);

        $data = $request->validate([
            'loket_id' => ['required', 'integer'],
        ]);

        $loket = Loket::where('is_active', true)->whereNotNull('poli_id')->whereKey($data['loket_id'])->first();

        abort_unless($loket && ($user->canAccessAllClusters() || (int) $user->loket_id === (int) $loket->id), 403);

        $request->session()->put('selected_loket_id', $loket->id);

        return redirect()->route('loket.index');
    }

    public function data(Request $request): JsonResponse
    {
        abort_unless($request->user()?->isLoket(), 403);

        $loket = $this->resolveLoket($request);

        $calling = Antrian::whereDate('tanggal', today())
            ->where('status', 'calling')
            ->where('loket_id', $loket->id)
            ->latest('called_at')
            ->first();

        $antrian = $calling ?: null;

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

        $doneToday = Antrian::whereDate('tanggal', today())
            ->where('poli_id', $loket->poli_id)
            ->whereIn('status', ['done', 'skipped'])
            ->orderByDesc('updated_at')
            ->take(20)
            ->get()
            ->map(fn (Antrian $a) => [
                'no_antrian' => $a->no_antrian,
                'status' => $a->status,
                'waktu' => $a->updated_at->format('H:i'),
            ]);

        return response()->json([
            'current' => $antrian ? [
                'id' => $antrian->id,
                'no_antrian' => $antrian->no_antrian,
                'nama_pasien' => $antrian->nama_pasien,
                'called_at' => $antrian->called_at?->format('H:i:s'),
            ] : null,
            'waiting' => $waiting,
            'waiting_count' => $waiting->count(),
            'done_today' => $doneToday,
        ]);
    }

    public function panggil(Request $request): JsonResponse
    {
        abort_unless($request->user()?->isLoket(), 403);

        $loket = $this->resolveLoket($request);
        $now = now();

        $next = Antrian::whereDate('tanggal', today())
            ->where('status', 'waiting')
            ->where('poli_id', $loket->poli_id)
            ->orderBy('nomor')
            ->first();

        if (! $next) {
            return response()->json(['message' => 'Tidak ada antrian yang menunggu.'], 404);
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
            'message' => 'Memanggil '.$next->no_antrian,
            'antrian' => [
                'id' => $next->id,
                'no_antrian' => $next->no_antrian,
                'nama_pasien' => $next->nama_pasien,
                'nama_poli' => $next->poli->nama,
                'loket' => $loket->nama,
            ],
        ]);
    }

    public function panggilUlang(Request $request): JsonResponse
    {
        abort_unless($request->user()?->isLoket(), 403);

        $loket = $this->resolveLoket($request);

        $calling = Antrian::whereDate('tanggal', today())
            ->where('status', 'calling')
            ->where('loket_id', $loket->id)
            ->latest('called_at')
            ->first();

        if (! $calling) {
            return response()->json(['message' => 'Tidak ada nomor yang sedang dipanggil.'], 404);
        }

        $calling->update([
            'called_at' => now(),
            'finished_at' => null,
        ]);

        return response()->json([
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

    public function selesai(Request $request): JsonResponse
    {
        abort_unless($request->user()?->isLoket(), 403);

        $loket = $this->resolveLoket($request);
        $now = now();

        $calling = Antrian::whereDate('tanggal', today())
            ->where('status', 'calling')
            ->where('loket_id', $loket->id)
            ->latest('called_at')
            ->first();

        if (! $calling) {
            return response()->json(['message' => 'Tidak ada nomor yang sedang dipanggil.'], 404);
        }

        $calling->update([
            'status' => 'done',
            'finished_at' => $now,
        ]);

        return response()->json(['message' => $calling->no_antrian.' selesai dilayani.']);
    }

    public function lewati(Request $request): JsonResponse
    {
        abort_unless($request->user()?->isLoket(), 403);

        $loket = $this->resolveLoket($request);

        $calling = Antrian::whereDate('tanggal', today())
            ->where('status', 'calling')
            ->where('loket_id', $loket->id)
            ->latest('called_at')
            ->first();

        if (! $calling) {
            return response()->json(['message' => 'Tidak ada nomor yang sedang dipanggil.'], 404);
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

            return response()->json(['message' => 'Lewati '.$calling->no_antrian.'. Memanggil '.$next->no_antrian]);
        }

        return response()->json(['message' => 'Lewati '.$calling->no_antrian.'. Antrian kosong.']);
    }

    private function resolveLoket(Request $request): Loket
    {
        $user = $request->user();
        abort_unless($user?->isLoket(), 403);

        $query = Loket::where('is_active', true)->whereNotNull('poli_id')->with('poli');

        if ($user->canAccessAllClusters()) {
            $selectedLoketId = $request->session()->get('selected_loket_id');

            if ($selectedLoketId) {
                $query->whereKey($selectedLoketId);
            }
        } else {
            $query->whereKey($user->loket_id);
        }

        $loket = $query->first();

        if (! $loket && $user->canAccessAllClusters()) {
            $loket = Loket::where('is_active', true)->whereNotNull('poli_id')->with('poli')->orderBy('nama')->first();
            $request->session()->forget('selected_loket_id');
        }

        abort_unless($loket, 403, 'Loket tidak terkonfigurasi atau tidak aktif. Hubungi administrator.');

        return $loket;
    }
}
