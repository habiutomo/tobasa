<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layar Antrian - {{ $namaPuskesmas }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body {
            height: 100%;
            overflow: hidden;
            background: #0f2a5a;
            font-family: "Segoe UI", system-ui, sans-serif;
        }
        .header {
            background: linear-gradient(90deg, #0d6efd, #0a4fb0);
            color: #fff;
            padding: 18px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 4px solid #ffc107;
        }
        .header .title { font-size: 2rem; font-weight: 800; letter-spacing: 1px; }
        .header .clock { text-align: right; }
        .header .clock .waktu { font-size: 2.4rem; font-weight: 800; font-variant-numeric: tabular-nums; }
        .header .clock .tanggal { font-size: 1rem; opacity: .9; }

        .loket-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 18px;
            padding: 22px 28px;
            flex: 1;
        }
        .loket-card {
            background: #fff;
            border-radius: 18px;
            padding: 18px 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 6px;
            box-shadow: 0 8px 18px rgba(0,0,0,.25);
            border-left: 8px solid #6c757d;
        }
        .loket-card .name { font-size: 1.1rem; font-weight: 700; color: #333; }
        .loket-card .poli-label { font-size: .95rem; color: #6c757d; }
        .loket-card .angka {
            font-size: 6.5rem;
            font-weight: 900;
            line-height: 1;
            color: #343a40;
            font-variant-numeric: tabular-nums;
        }
        .loket-card.empty .angka { color: #ced4da; font-size: 4.5rem; }
        .loket-card.calling { border-left-color: #0d6efd; background: #eef4ff; }
        .loket-card.calling .angka { color: #0d6efd; animation: blink 1s step-start infinite; }
        @keyframes blink { 50% { opacity: .25; } }

        .section-right {
            width: 360px;
            background: rgba(255,255,255,.06);
            padding: 20px 24px;
            color: #fff;
            overflow-y: auto;
        }
        .section-right h4 {
            font-size: 1.1rem;
            font-weight: 800;
            letter-spacing: 2px;
            margin-bottom: 16px;
            text-transform: uppercase;
            color: #ffc107;
        }
        .section-right .served-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 12px;
            margin-bottom: 8px;
            background: rgba(255,255,255,.08);
            border-radius: 10px;
            font-weight: 700;
            font-size: 1.05rem;
        }
        .served-item .no { font-size: 1.25rem; }
        .badge-status { font-size: .72rem; font-weight: 700; padding: 3px 10px; border-radius: 20px; }

        .marquee-wrap {
            background: #ffc107;
            color: #000;
            padding: 10px 0;
            white-space: nowrap;
            overflow: hidden;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
        }
        .marquee-wrap span {
            display: inline-block;
            padding-left: 100%;
            animation: marquee 25s linear infinite;
            font-weight: 700;
            font-size: 1.05rem;
        }
        @keyframes marquee { 0% { transform: translateX(0); } 100% { transform: translateX(-100%); } }

        .main-row {
            display: flex;
            height: calc(100vh - 110px - 46px);
        }
        .loket-area { flex: 1; display: flex; flex-direction: column; }

        .announce-pill {
            position: fixed;
            top: 92px;
            right: 24px;
            z-index: 1200;
            background: linear-gradient(90deg, #0d6efd, #0a4fb0);
            color: #fff;
            padding: 12px 22px;
            border-radius: 30px;
            font-weight: 800;
            font-size: 1.15rem;
            box-shadow: 0 6px 16px rgba(0,0,0,.35);
            animation: pop .3s ease;
            border: 1px solid rgba(255,255,255,.4);
        }
        @keyframes pop { from { transform: translateY(-10px); opacity: 0; } }
    </style>
</head>
<body>
    <div class="header">
        <div class="title"><i class="bi bi-hospital"></i> {{ $namaPuskesmas }}</div>
        <div class="clock d-flex align-items-center justify-content-end gap-2">
            <button id="muteBtn" class="btn btn-warning btn-sm fw-bold d-flex align-items-center" onclick="toggleMute()" title="Aktifkan / matikan suara">
                <i class="bi bi-volume-up" id="muteIcon"></i>
            </button>
            <div>
                <div class="tanggal" id="tanggal"></div>
                <div class="waktu" id="jam"></div>
            </div>
        </div>
    </div>

    <div id="announcePill" class="announce-pill d-none">
        <i class="bi bi-broadcast"></i> <span id="announceText"></span>
    </div>

    <div class="main-row">
        <div class="loket-area">
            <div class="loket-grid" id="loketGrid"></div>

            <div style="padding: 0 28px 18px; color:#fff;">
                <div class="d-flex justify-content-around align-items-center">
                    <div class="text-center"><div class="fs-6 opacity-75">Antrian Dipanggil</div><div class="fw-bold fs-4 text-warning" id="statCalling">0</div></div>
                    <div class="text-center"><div class="fs-6 opacity-75">Selesai</div><div class="fw-bold fs-4 text-success" id="statDone">0</div></div>
                    <div class="text-center"><div class="fs-6 opacity-75">Menunggu</div><div class="fw-bold fs-4 text-info" id="statWaiting">0</div></div>
                    <a href="{{ route('kiosk.index') }}" target="_blank" class="btn btn-outline-light btn-lg"><i class="bi bi-three-dots"></i> Ambil Antrian</a>
                </div>
            </div>
        </div>

        <div class="section-right">
            <h4>Nomor Terpanggil</h4>
            <div id="lastServed"></div>
        </div>
    </div>

    <div class="marquee-wrap">
        <span>{{ $marqueeText ?: 'Selamat datang di pelayanan kesehatan kami.' }}</span>
    </div>

    <div id="unlockOverlay" style="position:fixed;inset:0;z-index:2000;background:rgba(8,20,45,.88);display:flex;align-items:center;justify-content:center;">
        <div class="text-center text-white p-4" style="max-width:480px;">
            <i class="bi bi-volume-up-fill" style="font-size:4rem;color:#ffc107;"></i>
            <h2 class="fw-bold mt-3">Tekan untuk Mengaktifkan Suara</h2>
            <p class="text-white-50">Nada panggilan dan pesan suara antrian baru aktif setelah diklik.</p>
            <button class="btn btn-warning btn-lg fw-bold mt-2" onclick="unlockAudio()"><i class="bi bi-unlock-fill"></i> Aktifkan Suara</button>
        </div>
    </div>

    <script>
        const loketGrid = document.getElementById('loketGrid');
        const lastServed = document.getElementById('lastServed');
        const announcePill = document.getElementById('announcePill');
        const announceText = document.getElementById('announceText');
        const muteIcon = document.getElementById('muteIcon');

        let announced = {};
        let localMute = false;
        let audioCtx = null;
        let speaking = false;
        let pendingList = [];
        let pillTimer = null;
        let currentAudio = null;

        const voiceCfg = {
            active: {{ $suaraAktif ? 'true' : 'false' }},
            volume: {{ $volumeSuara }},
            repeat: {{ $ulangSuara }}
        };

        function effectiveActive() {
            return !localMute && voiceCfg.active;
        }

        function updateMuteIcon() {
            muteIcon.className = effectiveActive() ? 'bi bi-volume-up' : 'bi bi-volume-mute-fill';
        }

        function ensureAudio() {
            if (!audioCtx) {
                const AC = window.AudioContext || window.webkitAudioContext;
                if (!AC) return null;
                audioCtx = new AC();
            }
            if (audioCtx.state === 'suspended') audioCtx.resume();
            return audioCtx;
        }

        function unlockAudio() {
            document.getElementById('unlockOverlay').style.display = 'none';
            ensureAudio();
            flushPending();
        }

        function toggleMute() {
            localMute = !localMute;
            updateMuteIcon();
            if (localMute) {
                if (currentAudio) { try { currentAudio.pause(); } catch (e) {} }
                pendingList = [];
                speaking = false;
                if ('speechSynthesis' in window) window.speechSynthesis.cancel();
                hidePill();
            } else {
                flushPending();
            }
        }

        function bell(ctx, freq, time, dur, vol) {
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

        function playChime(cb) {
            if (!voiceCfg.active || localMute) { cb && cb(); return; }
            try {
                const ctx = ensureAudio();
                if (!ctx) { cb && cb(); return; }
                const t = ctx.currentTime + 0.03;
                const v = 0.32 * (voiceCfg.volume / 100);
                bell(ctx, 1046.50, t, 0.55, v);
                bell(ctx, 1046.50, t + 0.28, 0.55, v);
                bell(ctx, 783.99, t + 0.56, 1.05, v);
                setTimeout(cb, 1750);
            } catch (e) { cb && cb(); }
        }

        function pickVoice() {
            try {
                const voices = window.speechSynthesis.getVoices();
                return voices.find(v => v.lang && v.lang.toLowerCase().replace('_', '-').startsWith('id')) || null;
            } catch (e) { return null; }
        }

        function googleTtsUrl(text) {
            return 'https://translate.googleapis.com/translate_tts?client=gtx&ie=UTF-8&tl=id&ttsspeed=1&q=' +
                encodeURIComponent(text);
        }

        function speechFallback(text, done) {
            if (!('speechSynthesis' in window)) { done(); return; }
            const u = new SpeechSynthesisUtterance(text);
            u.lang = 'id-ID';
            u.rate = 0.92;
            u.pitch = 1;
            u.volume = Math.max(0, Math.min(1, voiceCfg.volume / 100));
            const vv = pickVoice();
            if (vv) u.voice = vv;
            u.onend = () => done();
            u.onerror = () => done();
            window.speechSynthesis.speak(u);
        }

        function playHuman(text, done) {
            let settled = false;
            let timer = null;
            const finish = (err) => {
                if (settled) return;
                settled = true;
                clearTimeout(timer);
                done(err);
            };
            try {
                const audio = new Audio(googleTtsUrl(text));
                currentAudio = audio;
                audio.preload = 'auto';
                audio.volume = Math.max(0, Math.min(1, voiceCfg.volume / 100));
                audio.onended = () => finish(false);
                audio.onerror = () => finish(true);
                audio.play().then(() => {
                    timer = setTimeout(() => finish(true), 15000);
                }).catch(() => finish(true));
            } catch (e) {
                finish(true);
            }
        }

        function flushPending() {
            if (speaking || pendingList.length === 0) return;
            if (!effectiveActive()) { pendingList = []; hidePill(); return; }
            const item = pendingList.shift();
            speaking = true;
            showPill(item.number);
            playChime(() => {
                if (!effectiveActive()) { speaking = false; hidePill(); flushPending(); return; }
                playHuman(item.text, (err) => {
                    if (err) {
                        speechFallback(item.text, () => {
                            speaking = false;
                            hidePill();
                            flushPending();
                        });
                    } else {
                        speaking = false;
                        hidePill();
                        flushPending();
                    }
                });
            });
        }

        function announceBatch(list) {
            pendingList.push(...list);
            flushPending();
        }

        function showPill(no) {
            announceText.textContent = 'Mengumumkan: ' + no;
            announcePill.classList.remove('d-none');
            clearTimeout(pillTimer);
            pillTimer = setTimeout(hidePill, 8000);
        }
        function hidePill() { announcePill.classList.add('d-none'); }

        const digitWords = ['nol', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan'];
        function spellNumber(no) {
            const s = String(no);
            const letters = (s.match(/[A-Za-z]+/) || [''])[0].toUpperCase();
            const digits = (s.match(/\d+/g) || [''])[0] || '';
            const parts = [];
            if (letters) parts.push(letters);
            for (const ch of digits) parts.push(digitWords[+ch] !== undefined ? digitWords[+ch] : ch);
            return parts.join(', ');
        }

        function updateClock() {
            const now = new Date();
            const jam = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            const tanggal = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
            document.getElementById('jam').textContent = jam;
            document.getElementById('tanggal').textContent = tanggal;
        }

        function render(data) {
            if (typeof data.suara_aktif !== 'undefined') voiceCfg.active = !!data.suara_aktif;
            if (typeof data.volume_suara !== 'undefined') voiceCfg.volume = Number(data.volume_suara);
            if (typeof data.ulang_suara !== 'undefined') voiceCfg.repeat = Number(data.ulang_suara);
            if (!voiceCfg.active) document.getElementById('unlockOverlay').style.display = 'none';
            updateMuteIcon();

            let html = '';
            const newAnnouncements = [];
            data.lokets.forEach(l => {
                const stateClass = l.is_calling ? 'calling' : 'empty';
                const angka = l.nomor_dipanggil ? l.nomor_dipanggil : '--';
                html += `
                    <div class="loket-card ${stateClass}">
                        <div class="name">${l.nama}</div>
                        <div class="poli-label">${l.poli ? l.poli : ''}</div>
                        <div class="angka">${angka}</div>
                        <div class="poli-label">${l.is_calling ? `<i class="bi bi-broadcast"></i> Sedang Dipanggil` : '&nbsp;'}</div>
                    </div>`;
                if (l.is_calling && l.nomor_dipanggil && l.called_at) {
                    const key = l.id + '|' + l.nomor_dipanggil + '|' + l.called_at;
                    if (!announced[key]) {
                        announced[key] = true;
                        for (let r = 0; r < voiceCfg.repeat; r++) {
                            newAnnouncements.push({
                                number: l.nomor_dipanggil,
                                text: `Nomor ${spellNumber(l.nomor_dipanggil)}, silakan menuju ${l.nama}.`
                            });
                        }
                    }
                }
            });
            loketGrid.innerHTML = html;
            if (newAnnouncements.length) announceBatch(newAnnouncements);

            let served = '';
            data.last_served.forEach(s => {
                const badge = s.status === 'done'
                    ? '<span class="badge-status bg-success">Selesai</span>'
                    : s.status === 'skipped'
                        ? '<span class="badge-status bg-warning text-dark">Lewat</span>'
                        : '<span class="badge-status bg-info text-dark">Dipanggil</span>';
                served += `<div class="served-item"><span class="no">${s.no_antrian}</span><span class="text-muted small">${s.nama_poli}</span>${badge}</div>`;
            });
            lastServed.innerHTML = served || '<div class="text-muted">Belum ada antrian</div>';
        }

        async function load() {
            try {
                const res = await fetch('{{ route('display.data') }}');
                const data = await res.json();
                render(data);
            } catch (e) {
                console.error(e);
            }
        }

        updateClock();
        setInterval(updateClock, 1000);
        load();
        setInterval(load, 2000);
    </script>
</body>
</html>