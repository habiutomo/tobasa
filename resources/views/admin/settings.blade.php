@extends('layouts.app')

@section('title', 'Pengaturan')
@section('pageTitle', 'Pengaturan')
@section('pageSubtitle', 'Konfigurasi umum sistem antrian')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-bold"><i class="bi bi-gear"></i> Pengaturan Umum</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.setting.update') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nama Puskesmas</label>
                        <input type="text" name="nama_puskesmas" class="form-control" value="{{ $namaPuskesmas }}" required>
                        <div class="form-text">Ditampilkan di layar antrian dan kiosk</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Teks Berjalan (Marquee)</label>
                        <input type="text" name="marquee_text" class="form-control" value="{{ $marqueeText }}" maxlength="255">
                        <div class="form-text">Teks selamat datang di bawah layar antrian</div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="form-label">Jam Buka</label>
                                <input type="time" name="jam_buka" class="form-control" value="{{ $jamBuka }}">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="form-label">Jam Tutup</label>
                                <input type="time" name="jam_tutup" class="form-control" value="{{ $jamTutup }}">
                            </div>
                        </div>
                    </div>
                    <div class="form-check form-switch mb-4">
                        <input type="checkbox" name="kiosk_open" value="1" class="form-check-input" id="kioskOpen" {{ $kioskOpen ? 'checked' : '' }}>
                        <label class="form-check-label" for="kioskOpen">
                            <strong>Kiosk Pengambilan Antrian Aktif</strong>
                            <span class="d-block text-muted small">Jika nonaktif, pasien tidak dapat mengambil nomor antrian baru</span>
                        </label>
                    </div>

                    <div class="border-top pt-4 mb-4">
                        <h5 class="fw-bold mb-3"><i class="bi bi-volume-up"></i> Suara Panggilan Antrian</h5>
                        <div class="form-check form-switch mb-3">
                            <input type="checkbox" name="suara_aktif" value="1" class="form-check-input" id="suaraAktif" {{ $suaraAktif ? 'checked' : '' }}>
                            <label class="form-check-label" for="suaraAktif">
                                <strong>Aktifkan suara di layar panggilan</strong>
                                <span class="d-block text-muted small">Nada panggilan dan pesan suara diputar di layar antrian saat nomor dipanggil</span>
                            </label>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Volume Suara: <span id="volLabel" class="fw-bold"></span>%</label>
                            <input type="range" name="volume_suara" min="0" max="100" value="{{ $volumeSuara }}" class="form-range" id="volRange">
                            <div class="form-text">Kekerasan suara panggilan di layar antrian</div>
                        </div>
                        <div class="mb-3 d-flex align-items-center gap-3">
                            <button type="button" class="btn btn-outline-warning fw-bold" onclick="playSample()">
                                <i class="bi bi-ear-speaker"></i> Coba Suara
                            </button>
                            <span class="text-muted small"><i class="bi bi-info-circle"></i> Putar contoh pengumuman dengan suara manusia sesuai volume di atas (butuh internet)</span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pengulangan Pengumuman</label>
                            <select name="ulang_suara" class="form-select">
                                <option value="1" {{ $ulangSuara == 1 ? 'selected' : '' }}>1 kali</option>
                                <option value="2" {{ $ulangSuara == 2 ? 'selected' : '' }}>2 kali</option>
                                <option value="3" {{ $ulangSuara == 3 ? 'selected' : '' }}>3 kali</option>
                            </select>
                            <div class="form-text">Nomor panggilan diumumkan berulang agar lebih terdengar jelas</div>
                        </div>
                    </div>

                    <button class="btn btn-primary w-100">Simpan Pengaturan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const volRange = document.getElementById('volRange');
    const volLabel = document.getElementById('volLabel');
    volLabel.textContent = volRange.value;
    volRange.addEventListener('input', () => { volLabel.textContent = volRange.value; });

    const digitWords = ['nol', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan'];
    function sampleSpell(no) {
        const letters = (no.match(/[A-Za-z]+/) || [''])[0].toUpperCase();
        const digits = (no.match(/\d+/g) || [''])[0] || '';
        const parts = [];
        if (letters) parts.push(letters);
        for (const ch of digits) parts.push(digitWords[+ch] !== undefined ? digitWords[+ch] : ch);
        return parts.join(', ');
    }
    function sampleTTS(text) {
        return 'https://translate.googleapis.com/translate_tts?client=gtx&ie=UTF-8&tl=id&q=' + encodeURIComponent(text);
    }
    function sampleBell(ctx, freq, time, dur, vol) {
        const o = ctx.createOscillator();
        const o2 = ctx.createOscillator();
        const g = ctx.createGain();
        const g2 = ctx.createGain();
        o.type = 'sine'; o.frequency.value = freq;
        o2.type = 'sine'; o2.frequency.value = freq * 1.007;
        g.gain.setValueAtTime(0.0001, time);
        g.gain.exponentialRampToValueAtTime(Math.max(vol, 0.0001), time + 0.02);
        g.gain.exponentialRampToValueAtTime(0.0001, time + dur);
        g2.gain.setValueAtTime(0.0001, time);
        g2.gain.exponentialRampToValueAtTime(Math.max(vol * 0.5, 0.0001), time + 0.02);
        g2.gain.exponentialRampToValueAtTime(0.0001, time + dur * 0.65);
        o.connect(g); g.connect(ctx.destination);
        o2.connect(g2); g2.connect(ctx.destination);
        o.start(time); o.stop(time + dur + 0.05);
        o2.start(time); o2.stop(time + dur * 0.7 + 0.05);
    }

    function playSample() {
        const vol = Number(volRange.value) / 100;
        let bellWait = 0;
        try {
            const AC = window.AudioContext || window.webkitAudioContext;
            if (AC) {
                const ctx = new AC();
                const t = ctx.currentTime + 0.03;
                const v = 0.32 * vol;
                sampleBell(ctx, 1046.50, t, 0.55, v);
                sampleBell(ctx, 1046.50, t + 0.28, 0.55, v);
                sampleBell(ctx, 783.99, t + 0.56, 1.05, v);
                bellWait = 1500;
                setTimeout(() => { try { ctx.close(); } catch (e) {} }, 1800);
            }
        } catch (e) { bellWait = 0; }

        const text = 'Nomor ' + sampleSpell('A001') + ', silakan menuju Loket 1.';
        setTimeout(() => {
            const audio = new Audio(sampleTTS(text));
            audio.volume = Math.max(0, Math.min(1, vol));
            audio.play().catch(() => alert('Tidak dapat memutar sampel. Pastikan halaman ini memiliki koneksi internet.'));
        }, bellWait);
    }
</script>
@endpush