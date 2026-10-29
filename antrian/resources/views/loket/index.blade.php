@extends('layouts.app')

@section('title', 'Loket - ' . $loket->nama)
@section('pageTitle', $loket->nama)
@section('pageSubtitle', 'Panggil dan kelola antrian pasien')

@push('styles')
<style>
    .current-box {
        border-radius: 18px;
        border: 3px dashed #0d6efd;
        background: #eef4ff;
        padding: 28px;
        text-align: center;
    }
    .current-box .label { font-size: 1rem; color: #6c757d; font-weight: 600; letter-spacing: 2px; text-transform: uppercase; }
    .current-box .no {
        font-size: 6rem;
        font-weight: 900;
        color: #0d6efd;
        line-height: 1.1;
        font-variant-numeric: tabular-nums;
    }
    .current-box.empty { border-color: #adb5bd; background: #f8f9fa; }
    .current-box.empty .no { color: #adb5bd; }
    .action-btn {
        padding: 16px;
        font-weight: 700;
        font-size: 1.05rem;
        border-radius: 14px;
    }
    .queue-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 14px;
        border-bottom: 1px solid #eee;
    }
    .queue-item .no { font-weight: 800; font-size: 1.15rem; }
    .pulse-ring {
        animation: pulseRing 1.4s ease-out infinite;
    }
    @keyframes pulseRing {
        0% { transform: scale(.97); opacity: .6; }
        70% { transform: scale(1.03); opacity: 1; }
        100% { transform: scale(.97); opacity: .6; }
    }
</style>
@endpush

@section('content')
@if($allClusters)
<div class="card shadow-sm mb-4">
    <div class="card-body d-flex flex-column flex-md-row align-items-md-center gap-3">
        <div>
            <div class="fw-bold"><i class="bi bi-diagram-3"></i> Pilih Klaster / Loket</div>
            <div class="text-muted small">Petugas ini dapat mengelola seluruh klaster aktif.</div>
        </div>
        <form method="POST" action="{{ route('loket.pilih') }}" class="d-flex flex-column flex-sm-row gap-2 ms-md-auto">
            @csrf
            <select name="loket_id" class="form-select" onchange="this.form.submit()">
                @foreach($availableLokets as $availableLoket)
                    <option value="{{ $availableLoket->id }}" {{ $availableLoket->id === $loket->id ? 'selected' : '' }}>
                        {{ $availableLoket->nama }} — {{ $availableLoket->poli?->nama }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-outline-primary">Ganti</button>
        </form>
    </div>
</div>
@endif
<div class="row g-4">
    <div class="col-lg-5">
        <div class="current-box {{ empty($current) ? 'empty' : '' }}" id="currentBox">
            <div class="label mb-2"><i class="bi bi-broadcast"></i> Sedang Dipanggil</div>
            <div class="no" id="currentNo">----</div>
            <div class="fs-5" id="currentName">&nbsp;</div>
            <div class="text-muted small" id="currentTime">&nbsp;</div>
        </div>

        <div class="mt-4">
            <button class="btn btn-primary btn-lg w-100 action-btn mb-2" onclick="action('{{ route('loket.panggil') }}')">
                <i class="bi bi-phone-vibrate"></i> Panggil Berikutnya
            </button>
            <div class="row g-2">
                <div class="col-6">
                    <button class="btn btn-warning w-100 action-btn" onclick="action('{{ route('loket.panggil-ulang') }}')">
                        <i class="bi bi-arrow-repeat"></i> Panggil Ulang
                    </button>
                </div>
                <div class="col-6">
                    <button class="btn btn-danger w-100 action-btn" onclick="action('{{ route('loket.lewati') }}')">
                        <i class="bi bi-skip-forward-circle"></i> Lewati
                    </button>
                </div>
            </div>
            <button class="btn btn-success btn-lg w-100 action-btn mt-2" onclick="action('{{ route('loket.selesai') }}')">
                <i class="bi bi-check2-circle"></i> Selesai Dilayani
            </button>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="bi bi-list-ol"></i> Antrian Menunggu <span id="waitCount" class="badge bg-primary"></span></span>
                <button class="btn btn-sm btn-outline-secondary" onclick="loadData()"><i class="bi bi-arrow-clockwise"></i> Segarkan</button>
            </div>
            <div class="card-body p-0" id="waitingList" style="max-height: 340px; overflow-y:auto;">
                <div class="text-center text-muted p-4">Memuat...</div>
            </div>
        </div>

        <div class="card shadow-sm mt-3">
            <div class="card-header bg-white fw-bold"><i class="bi bi-check2-all"></i> Antrian Hari Ini</div>
            <div class="card-body p-0" id="doneList" style="max-height: 220px; overflow-y:auto;">
                <div class="text-center text-muted p-4">Memuat...</div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let audioOk = true;

    function chime() {
        if (!audioOk) return;
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const t = ctx.currentTime + 0.03;
            const notes = [[880, t, 0.4], [1174.66, t + 0.15, 0.4], [1567.98, t + 0.3, 0.5]];
            notes.forEach(([f, time, dur]) => {
                const o = ctx.createOscillator();
                const g = ctx.createGain();
                o.type = 'sine'; o.frequency.value = f;
                g.gain.setValueAtTime(0.0001, time);
                g.gain.exponentialRampToValueAtTime(0.3, time + 0.02);
                g.gain.exponentialRampToValueAtTime(0.0001, time + dur);
                o.connect(g); g.connect(ctx.destination);
                o.start(time); o.stop(time + dur + 0.05);
            });
            setTimeout(() => { try { ctx.close(); } catch (e) {} }, 1000);
        } catch (e) { audioOk = false; }
    }

    function renderData(d) {
        if (d.current) {
            const box = document.getElementById('currentBox');
            box.classList.remove('empty');
            document.getElementById('currentNo').textContent = d.current.no_antrian;
            document.getElementById('currentName').textContent = d.current.nama_pasien ? ('Pasien: ' + d.current.nama_pasien) : '';
            document.getElementById('currentTime').textContent = 'Dipanggil pukul ' + d.current.called_at;
        } else {
            const box = document.getElementById('currentBox');
            box.classList.add('empty');
            document.getElementById('currentNo').textContent = '----';
            document.getElementById('currentName').innerHTML = '<span class="text-muted">Belum ada antrian dipanggil</span>';
            document.getElementById('currentTime').innerHTML = '&nbsp;';
        }

        document.getElementById('waitCount').textContent = d.waiting_count;
        const waiting = document.getElementById('waitingList');
        if (d.waiting.length === 0) {
            waiting.innerHTML = '<div class="text-center text-muted p-4">Tidak ada antrian menunggu</div>';
        } else {
            let html = '';
            d.waiting.forEach((w, i) => {
                const name = w.nama_pasien ? `<div class="text-muted small">${w.nama_pasien}</div>` : '';
                html += `
                    <div class="queue-item">
                        <div>
                            <div class="no">${i + 1}. ${w.no_antrian}</div>
                            ${name}
                        </div>
                    </div>`;
            });
            waiting.innerHTML = html;
        }

        const done = document.getElementById('doneList');
        if (d.done_today.length === 0) {
            done.innerHTML = '<div class="text-center text-muted p-4">Belum ada riwayat</div>';
        } else {
            let html = '';
            d.done_today.forEach(x => {
                const badge = x.status === 'done'
                    ? '<span class="badge bg-success">Selesai</span>'
                    : '<span class="badge bg-warning text-dark">Lewat</span>';
                html += `<div class="queue-item"><span class="no">${x.no_antrian}</span>${badge}<span class="text-muted small">${x.waktu}</span></div>`;
            });
            done.innerHTML = html;
        }
    }

    async function loadData() {
        try {
            const res = await fetch('{{ route('loket.data') }}', { headers: { 'Accept': 'application/json' } });
            const d = await res.json();
            renderData(d);
        } catch (e) { console.error(e); }
    }

    async function action(url) {
        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                }
            });
            const d = await res.json();
            if (!res.ok) {
                alert(d.message || 'Gagal memproses');
                return;
            }
            chime();
            loadData();
            if (d.antrian && d.antrian.no_antrian) {
                toast(d.antrian.no_antrian);
            }
        } catch (e) {
            alert('Terjadi kesalahan koneksi.');
        }
    }

    let toastTimer;
    function toast(msg) {
        const existing = document.getElementById('floatingToast');
        if (existing) existing.remove();
        const t = document.createElement('div');
        t.id = 'floatingToast';
        t.className = 'pulse-ring';
        t.style.cssText = 'position:fixed;top:20px;right:20px;background:#0d6efd;color:#fff;border-radius:14px;padding:18px 26px;font-size:2rem;font-weight:800;z-index:2000;box-shadow:0 8px 20px rgba(0,0,0,.3);';
        t.textContent = msg;
        document.body.appendChild(t);
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => t.remove(), 2500);
    }

    loadData();
    setInterval(loadData, 5000);
</script>
@endpush