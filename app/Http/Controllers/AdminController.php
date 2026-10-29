<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use App\Models\Klaster;
use App\Models\Loket;
use App\Models\Poli;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        abort_if(! Auth::user()->isAdmin(), 403);

        $today = today()->toDateString();

        return view('admin.dashboard', [
            'totalHariIni' => Antrian::whereDate('tanggal', $today)->count(),
            'menunggu' => Antrian::whereDate('tanggal', $today)->where('status', 'waiting')->count(),
            'dipanggil' => Antrian::whereDate('tanggal', $today)->where('status', 'calling')->count(),
            'selesai' => Antrian::whereDate('tanggal', $today)->where('status', 'done')->count(),
            'lewat' => Antrian::whereDate('tanggal', $today)->where('status', 'skipped')->count(),
            'antrians' => Antrian::whereDate('tanggal', $today)
                ->with('poli', 'loket')
                ->latest()
                ->take(50)
                ->get(),
        ]);
    }

    // ------ Polis ------
    public function polis(): View
    {
        abort_if(! Auth::user()->isAdmin(), 403);

        return view('admin.polis', [
            'polis' => Poli::with('klaster')->orderBy('prefix')->get(),
            'klasters' => Klaster::orderBy('urutan')->get(),
        ]);
    }

    public function polisStore(Request $request): RedirectResponse
    {
        abort_if(! Auth::user()->isAdmin(), 403);

        $data = $request->validate([
            'klaster_id' => 'required|exists:klasters,id',
            'nama' => 'required|string|max:100',
            'prefix' => 'required|string|max:4|unique:polis,prefix',
            'warna' => 'required|string|max:20',
            'deskripsi' => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
        ]);

        Poli::create($data);

        return back()->with('success', 'Layanan berhasil ditambahkan.');
    }

    public function polisUpdate(Request $request, Poli $poli): RedirectResponse
    {
        abort_if(! Auth::user()->isAdmin(), 403);

        $data = $request->validate([
            'klaster_id' => 'required|exists:klasters,id',
            'nama' => 'required|string|max:100',
            'prefix' => 'required|string|max:4|unique:polis,prefix,'.$poli->id,
            'warna' => 'required|string|max:20',
            'deskripsi' => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
        ]);

        $poli->update($data);

        return back()->with('success', 'Layanan berhasil diperbarui.');
    }

    public function polisDestroy(Poli $poli): RedirectResponse
    {
        abort_if(! Auth::user()->isAdmin(), 403);

        if ($poli->antrians()->exists() || $poli->lokets()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus layanan yang memiliki loket atau riwayat antrian.');
        }

        $poli->delete();

        return back()->with('success', 'Layanan berhasil dihapus.');
    }

    // ------ Klasters ------
    public function klasters(): View
    {
        abort_if(! Auth::user()->isAdmin(), 403);

        return view('admin.klasters', ['klasters' => Klaster::withCount('polis')->orderBy('urutan')->get()]);
    }

    public function klastersStore(Request $request): RedirectResponse
    {
        abort_if(! Auth::user()->isAdmin(), 403);

        $data = $request->validate([
            'nama' => 'required|string|max:150',
            'warna' => 'required|string|max:20',
            'urutan' => 'required|integer|min:0',
        ]);

        Klaster::create($data);

        return back()->with('success', 'Klaster berhasil ditambahkan.');
    }

    public function klastersUpdate(Request $request, Klaster $klaster): RedirectResponse
    {
        abort_if(! Auth::user()->isAdmin(), 403);

        $data = $request->validate([
            'nama' => 'required|string|max:150',
            'warna' => 'required|string|max:20',
            'urutan' => 'required|integer|min:0',
            'is_active' => 'sometimes|boolean',
        ]);

        $klaster->update($data);

        return back()->with('success', 'Klaster berhasil diperbarui.');
    }

    public function klastersDestroy(Klaster $klaster): RedirectResponse
    {
        abort_if(! Auth::user()->isAdmin(), 403);

        if ($klaster->polis()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus klaster yang masih memiliki layanan.');
        }

        $klaster->delete();

        return back()->with('success', 'Klaster berhasil dihapus.');
    }

    // ------ Lokets ------
    public function lokets(): View
    {
        abort_if(! Auth::user()->isAdmin(), 403);

        return view('admin.lokets', [
            'lokets' => Loket::with('poli')->orderBy('id')->get(),
            'polis' => Poli::where('is_active', true)->orderBy('prefix')->get(),
        ]);
    }

    public function loketsStore(Request $request): RedirectResponse
    {
        abort_if(! Auth::user()->isAdmin(), 403);

        $data = $request->validate([
            'nama' => 'required|string|max:100',
            'poli_id' => 'required|exists:polis,id',
        ]);

        Loket::create($data);

        return back()->with('success', 'Loket berhasil ditambahkan.');
    }

    public function loketsUpdate(Request $request, Loket $loket): RedirectResponse
    {
        abort_if(! Auth::user()->isAdmin(), 403);

        $data = $request->validate([
            'nama' => 'required|string|max:100',
            'poli_id' => 'required|exists:polis,id',
            'is_active' => 'sometimes|boolean',
        ]);

        $loket->update($data);

        return back()->with('success', 'Loket berhasil diperbarui.');
    }

    public function loketsDestroy(Loket $loket): RedirectResponse
    {
        abort_if(! Auth::user()->isAdmin(), 403);

        if ($loket->users()->exists() || $loket->antrians()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus loket yang memiliki petugas atau riwayat antrian.');
        }

        $loket->delete();

        return back()->with('success', 'Loket berhasil dihapus.');
    }

    // ------ Users ------
    public function users(): View
    {
        abort_if(! Auth::user()->isAdmin(), 403);

        return view('admin.users', [
            'users' => User::with('loket')->orderBy('id')->get(),
            'lokets' => Loket::where('is_active', true)->whereNotNull('poli_id')->with('poli')->orderBy('nama')->get(),
        ]);
    }

    public function usersStore(Request $request): RedirectResponse
    {
        abort_if(! Auth::user()->isAdmin(), 403);

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,loket',
            'loket_id' => [
                'nullable',
                Rule::exists('lokets', 'id')->where(fn ($query) => $query->where('is_active', true)->whereNotNull('poli_id')),
            ],
            'all_clusters' => 'sometimes|boolean',
        ]);

        $data['all_clusters'] = $data['role'] === 'loket' && $request->boolean('all_clusters');

        if ($data['all_clusters']) {
            $data['loket_id'] = null;
        }

        if ($data['role'] === 'loket' && ! $data['all_clusters'] && empty($data['loket_id'])) {
            return back()->withInput()->withErrors(['loket_id' => 'Pilih loket atau aktifkan akses semua klaster.']);
        }

        $data['password'] = bcrypt($data['password']);

        User::create($data);

        return back()->with('success', 'Petugas berhasil ditambahkan.');
    }

    public function usersUpdate(Request $request, User $user): RedirectResponse
    {
        abort_if(! Auth::user()->isAdmin(), 403);

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:admin,loket',
            'loket_id' => [
                'nullable',
                Rule::exists('lokets', 'id')->where(fn ($query) => $query->where('is_active', true)->whereNotNull('poli_id')),
            ],
            'all_clusters' => 'sometimes|boolean',
        ]);

        $data['all_clusters'] = $data['role'] === 'loket' && $request->boolean('all_clusters');

        if ($data['all_clusters']) {
            $data['loket_id'] = null;
        }

        if ($data['role'] === 'loket' && ! $data['all_clusters'] && empty($data['loket_id'])) {
            return back()->withInput()->withErrors(['loket_id' => 'Pilih loket atau aktifkan akses semua klaster.']);
        }

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = bcrypt($data['password']);
        }

        $user->update($data);

        return back()->with('success', 'Petugas berhasil diperbarui.');
    }

    public function usersDestroy(User $user): RedirectResponse
    {
        abort_if(! Auth::user()->isAdmin(), 403);

        if ($user->id === Auth::id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        $user->delete();

        return back()->with('success', 'Petugas berhasil dihapus.');
    }

    public function userTokenGenerate(User $user): RedirectResponse
    {
        abort_if(! Auth::user()->isAdmin(), 403);
        abort_unless($user->isLoket() && ($user->canAccessAllClusters() || $user->loket_id), 403, 'Token API hanya untuk petugas loket.');

        $token = Str::random(60);

        $user->update(['api_token' => $token]);

        return back()->with('success', 'Token API baru berhasil dibuat.')
            ->with('api_token', $token)
            ->with('api_token_user', $user->name);
    }

    public function userTokenRevoke(User $user): RedirectResponse
    {
        abort_if(! Auth::user()->isAdmin(), 403);

        $user->update(['api_token' => null]);

        return back()->with('success', 'Token API berhasil dinonaktifkan.');
    }

    // ------ Settings ------
    public function settings(): View
    {
        abort_if(! Auth::user()->isAdmin(), 403);

        return view('admin.settings', [
            'namaPuskesmas' => Setting::get('nama_puskesmas', ''),
            'marqueeText' => Setting::get('marquee_text', ''),
            'kioskOpen' => Setting::get('kiosk_open', '1') === '1',
            'jamBuka' => Setting::get('jam_buka', ''),
            'jamTutup' => Setting::get('jam_tutup', ''),
            'suaraAktif' => Setting::get('suara_aktif', '1') === '1',
            'volumeSuara' => (int) Setting::get('volume_suara', 100),
            'ulangSuara' => (int) Setting::get('ulang_suara', 1),
        ]);
    }

    public function settingsUpdate(Request $request): RedirectResponse
    {
        abort_if(! Auth::user()->isAdmin(), 403);

        $data = $request->validate([
            'nama_puskesmas' => 'required|string|max:100',
            'marquee_text' => 'nullable|string|max:255',
            'kiosk_open' => 'sometimes|boolean',
            'jam_buka' => 'nullable',
            'jam_tutup' => 'nullable',
            'suara_aktif' => 'sometimes|boolean',
            'volume_suara' => 'nullable|integer|min:0|max:100',
            'ulang_suara' => 'nullable|integer|min:1|max:3',
        ]);

        Setting::set('nama_puskesmas', $data['nama_puskesmas']);
        Setting::set('marquee_text', $data['marquee_text'] ?? '');
        Setting::set('kiosk_open', isset($data['kiosk_open']) ? '1' : '0');
        Setting::set('jam_buka', $data['jam_buka'] ?? '');
        Setting::set('jam_tutup', $data['jam_tutup'] ?? '');
        Setting::set('suara_aktif', isset($data['suara_aktif']) ? '1' : '0');
        Setting::set('volume_suara', (string) ($data['volume_suara'] ?? 100));
        Setting::set('ulang_suara', (string) ($data['ulang_suara'] ?? 1));

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }

    // ------ Laporan ------
    public function laporan(Request $request): View
    {
        abort_if(! Auth::user()->isAdmin(), 403);

        $tanggalAwal = $request->get('tanggal_awal', today()->copy()->startOfMonth()->toDateString());
        $tanggalAkhir = $request->get('tanggal_akhir', today()->toDateString());

        $query = Antrian::with('poli', 'loket')
            ->whereDate('tanggal', '>=', $tanggalAwal)
            ->whereDate('tanggal', '<=', $tanggalAkhir)
            ->orderBy('tanggal', 'desc')
            ->orderBy('poli_id')
            ->orderBy('nomor');

        $antrians = $query->get();

        $perPoli = $antrians->groupBy(fn ($a) => $a->poli?->nama ?? 'Tanpa Poli')
            ->map(fn ($items, $poli) => [
                'poli' => $poli,
                'total' => $items->count(),
                'selesai' => $items->where('status', 'done')->count(),
                'waiting' => $items->where('status', 'waiting')->count(),
                'dipanggil' => $items->where('status', 'calling')->count(),
                'lewat' => $items->where('status', 'skipped')->count(),
            ])->values();

        return view('admin.laporan', [
            'antrians' => $antrians,
            'perPoli' => $perPoli,
            'tanggalAwal' => $tanggalAwal,
            'tanggalAkhir' => $tanggalAkhir,
            'total' => $antrians->count(),
            'totalSelesai' => $antrians->where('status', 'done')->count(),
            'totalLewat' => $antrians->where('status', 'skipped')->count(),
        ]);
    }

    public function resetHariIni(): RedirectResponse
    {
        abort_if(! Auth::user()->isAdmin(), 403);

        Antrian::whereDate('tanggal', today())->delete();

        return back()->with('success', 'Antrian hari ini berhasil direset.');
    }
}
