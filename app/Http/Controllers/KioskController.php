<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use App\Models\Klaster;
use App\Models\Poli;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class KioskController extends Controller
{
    public function index(): Response
    {
        $kioskOpen = Setting::get('kiosk_open', '1') === '1';
        $namaPuskesmas = Setting::get('nama_puskesmas', 'PUSKESMAS');

        return response(view('kiosk.index', [
            'kioskOpen' => $kioskOpen,
            'namaPuskesmas' => $namaPuskesmas,
            'jamBuka' => Setting::get('jam_buka', '08:00'),
            'jamTutup' => Setting::get('jam_tutup', '14:00'),
        ]))->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function klasters(): JsonResponse
    {
        return response()->json(
            Klaster::where('is_active', true)
                ->with(['polis' => fn ($q) => $q->where('is_active', true)->orderBy('prefix')])
                ->orderBy('urutan')
                ->get()
                ->map(fn (Klaster $k) => [
                    'id' => $k->id,
                    'nama' => $k->nama,
                    'warna' => $k->warna,
                    'polis' => $k->polis->map(fn (Poli $p) => [
                        'id' => $p->id,
                        'nama' => $p->nama,
                        'prefix' => $p->prefix,
                        'warna' => $p->warna,
                        'deskripsi' => $p->deskripsi,
                    ]),
                ])
        );
    }

    public function store(Request $request): JsonResponse
    {
        if (Setting::get('kiosk_open', '1') !== '1') {
            return response()->json(['message' => 'Pengambilan antrian sedang ditutup.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'poli_id' => ['required', 'exists:polis,id,is_active,1'],
            'nama_pasien' => ['nullable', 'string', 'max:100'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Silakan pilih poli terlebih dahulu.', 'errors' => $validator->errors()], 422);
        }

        $poli = Poli::findOrFail($request->poli_id);

        try {
            $antrian = DB::transaction(function () use ($poli, $request) {
                $today = today()->toDateString();

                $last = Antrian::whereDate('tanggal', $today)
                    ->where('poli_id', $poli->id)
                    ->orderByDesc('nomor')
                    ->lockForUpdate()
                    ->first();

                $nomor = $last ? $last->nomor + 1 : 1;

                return Antrian::create([
                    'tanggal' => $today,
                    'poli_id' => $poli->id,
                    'nomor' => $nomor,
                    'no_antrian' => $poli->prefix.str_pad((string) $nomor, 3, '0', STR_PAD_LEFT),
                    'nama_pasien' => $request->nama_pasien ?: null,
                    'status' => 'waiting',
                ]);
            });

            $menunggu = Antrian::whereDate('tanggal', today())
                ->where('poli_id', $poli->id)
                ->where('status', 'waiting')
                ->count();

            $poliBaru = (object) ['nama' => $poli->nama, 'prefix' => $poli->prefix];

            return response()->json([
                'message' => 'Antrian berhasil diambil.',
                'antrian' => [
                    'no_antrian' => $antrian->no_antrian,
                    'nama_poli' => $poli->nama,
                    'nomor' => $antrian->nomor,
                    'sisa_menunggu' => $menunggu,
                    'nama_pasien' => $antrian->nama_pasien,
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Terjadi kesalahan, coba lagi.'], 500);
        }
    }
}
