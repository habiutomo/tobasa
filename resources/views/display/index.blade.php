<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layar Antrian - {{ $namaPuskesmas }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --bg-deep: #060d20;
            --bg-mid: #0b1a3a;
            --accent: #38bdf8;
            --accent-2: #6366f1;
            --ok: #22c55e;
            --warn: #fbbf24;
            --line: rgba(255, 255, 255, .10);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        html, body {
            height: 100%;
            overflow: hidden;
            background: var(--bg-deep);
            color: #e6edf7;
            font-family: "Segoe UI", system-ui, -apple-system, sans-serif;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            z-index: 0;
            background:
                radial-gradient(1100px 620px at 12% -10%, rgba(56, 189, 248, .22), transparent 60%),
                radial-gradient(900px 560px at 100% 0%, rgba(99, 102, 241, .20), transparent 62%),
                radial-gradient(900px 700px at 50% 120%, rgba(34, 197, 94, .12), transparent 60%),
                linear-gradient(180deg, var(--bg-mid) 0%, var(--bg-deep) 100%);
        }

        .app {
            position: relative;
            z-index: 1;
            height: 100vh;
            display: grid;
            grid-template-rows: auto 1fr auto;
        }

        /* ============ HEADER ============ */
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 16px 30px;
            background: linear-gradient(90deg, rgba(56, 189, 248, .16), rgba(99, 102, 241, .12) 45%, rgba(6, 13, 32, .05));
            border-bottom: 1px solid var(--line);
            box-shadow: 0 10px 30px rgba(0, 0, 0, .35);
        }

        .brand { display: flex; align-items: center; gap: 16px; min-width: 0; }

        .brand-mark {
            width: 58px; height: 58px;
            flex-shrink: 0;
            display: grid; place-items: center;
            border-radius: 18px;
            font-size: 1.9rem;
            color: #04121f;
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            box-shadow: 0 0 0 4px rgba(56, 189, 248, .14), 0 10px 24px rgba(56, 189, 248, .30);
        }

        .brand-text { min-width: 0; }
        .brand-eyebrow {
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: var(--accent);
        }
        .brand-title {
            font-size: 2rem;
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: .5px;
            color: #fff;
            text-shadow: 0 2px 12px rgba(0, 0, 0, .45);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }

        .header-right { display: flex; align-items: center; gap: 22px; }

        .sound-btn {
            width: 52px; height: 52px;
            display: grid; place-items: center;
            border-radius: 50%;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, .07);
            color: #fff;
            font-size: 1.35rem;
            cursor: pointer;
            transition: .2s;
        }
        .sound-btn:hover { background: rgba(255, 255, 255, .15); transform: translateY(-2px); }
        .sound-btn.muted { color: #94a3b8; background: rgba(255, 255, 255, .04); }

        .clock { text-align: right; }
        .clock .tanggal {
            font-size: .92rem;
            color: #a8bcd8;
            text-transform: capitalize;
        }
        .clock .waktu {
            font-size: 2.6rem;
            font-weight: 800;
            line-height: 1.05;
            font-variant-numeric: tabular-nums;
            background: linear-gradient(120deg, #fff, #7dd3fc);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* ============ MAIN ============ */
        .main {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 22px;
            padding: 22px 26px;
            min-height: 0;
        }

        .left { display: flex; flex-direction: column; gap: 20px; min-height: 0; }

        .panel {
            background: rgba(255, 255, 255, .045);
            border: 1px solid var(--line);
            border-radius: 22px;
            backdrop-filter: blur(8px);
        }

        .stat-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr) auto;
            gap: 14px;
            padding: 16px;
            flex-shrink: 0;
        }

        .stat {
            display: flex; align-items: center; gap: 14px;
            padding: 12px 16px;
            border-radius: 16px;
            background: rgba(255, 255, 255, .05);
            border: 1px solid var(--line);
            border-left: 4px solid var(--sc, #64748b);
        }
        .stat .ico {
            width: 44px; height: 44px; flex-shrink: 0;
            display: grid; place-items: center;
            border-radius: 13px;
            font-size: 1.25rem;
            color: var(--sc, #64748b);
            background: rgba(148, 163, 184, .18);
            background: color-mix(in srgb, var(--sc, #64748b) 18%, transparent);
        }
        .stat .val {
            font-size: 1.9rem; font-weight: 800; line-height: 1;
            font-variant-numeric: tabular-nums;
            color: #fff;
        }
        .stat .lbl {
            font-size: .74rem; letter-spacing: 1.4px; text-transform: uppercase;
            color: #93a8c6; font-weight: 700; margin-top: 4px;
        }

        .kiosk-btn {
            display: flex; align-items: center; gap: 10px;
            padding: 0 22px;
            border-radius: 16px;
            font-weight: 800;
            color: #04121f;
            text-decoration: none;
            white-space: nowrap;
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            box-shadow: 0 10px 26px rgba(56, 189, 248, .30);
            transition: .2s;
        }
        .kiosk-btn:hover { transform: translateY(-2px); color: #04121f; }
        .kiosk-btn .sub { display: block; font-size: .72rem; font-weight: 600; opacity: .75; }

        /* ---- kartu loket ---- */
        .loket-wrap { flex: 1; min-height: 0; overflow-y: auto; padding: 4px; }
        .loket-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 18px;
        }

        .loket-card {
            position: relative;
            overflow: hidden;
            padding: 20px 18px;
            border-radius: 22px;
            text-align: center;
            background: rgba(255, 255, 255, .05);
            border: 1px solid var(--line);
            box-shadow: 0 12px 30px rgba(0, 0, 0, .28);
            transition: transform .25s, box-shadow .25s, background .25s;
        }
        .loket-card::before {
            content: "";
            position: absolute; inset: 0 0 auto 0; height: 5px;
            background: var(--c, #64748b);
        }
        .loket-card::after {
            content: "";
            position: absolute; inset: 0;
            background: radial-gradient(420px 140px at 50% 0%, rgba(100, 116, 139, .22), transparent 70%);
            background: radial-gradient(420px 140px at 50% 0%, color-mix(in srgb, var(--c, #64748b) 26%, transparent), transparent 70%);
            pointer-events: none;
        }

        .loket-card .name {
            position: relative;
            font-size: 1.05rem; font-weight: 800; color: #fff;
            overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
        }
        .loket-card .poli {
            position: relative;
            display: inline-block;
            margin-top: 6px;
            font-size: .74rem; font-weight: 700;
            letter-spacing: .6px; text-transform: uppercase;
            color: #a9bedb;
        }
        .loket-card .angka {
            position: relative;
            margin: 10px 0 6px;
            font-size: 4.6rem; font-weight: 900; line-height: 1;
            letter-spacing: -1px;
            font-variant-numeric: tabular-nums;
            color: #fff;
            text-shadow: 0 6px 22px rgba(0, 0, 0, .5);
        }
        .loket-card .status {
            position: relative;
            display: inline-flex; align-items: center; gap: 6px;
            font-size: .76rem; font-weight: 700;
            letter-spacing: .8px; text-transform: uppercase;
            color: #7f93af;
        }
        .loket-card.idle { opacity: .92; }
        .loket-card.idle .angka { color: #4a5b74; text-shadow: none; }

        .loket-card.calling {
            background: rgba(56, 189, 248, .14);
            background: color-mix(in srgb, var(--c, #38bdf8) 16%, rgba(255, 255, 255, .05));
            border-color: rgba(56, 189, 248, .55);
            border-color: color-mix(in srgb, var(--c, #38bdf8) 55%, transparent);
            box-shadow: 0 0 0 2px rgba(56, 189, 248, .35), 0 16px 40px rgba(0, 0, 0, .45);
            box-shadow: 0 0 0 2px color-mix(in srgb, var(--c, #38bdf8) 35%, transparent), 0 16px 40px rgba(0, 0, 0, .45);
            animation: cardPulse 1.4s ease-in-out infinite;
        }
        .loket-card.calling .angka { animation: glowNum 1.4s ease-in-out infinite; }
        .loket-card.calling .status { color: #fff; }
        .loket-card.calling .status i { color: var(--c, #38bdf8); }

        @keyframes cardPulse {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }
        @keyframes glowNum {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: .55; transform: scale(1.05); }
        }

        /* ============ PANEL KANAN ============ */
        .right {
            display: flex; flex-direction: column;
            min-height: 0;
            padding: 20px 18px;
        }
        .right-head {
            display: flex; align-items: center; gap: 10px;
            padding-bottom: 14px; margin-bottom: 14px;
            border-bottom: 1px solid var(--line);
            flex-shrink: 0;
        }
        .right-head i { font-size: 1.25rem; color: var(--accent); }
        .right-head h4 {
            font-size: .95rem; font-weight: 800;
            letter-spacing: 2.4px; text-transform: uppercase; color: #cfe0f5;
        }

        .history { flex: 1; min-height: 0; overflow-y: auto; display: flex; flex-direction: column; gap: 9px; }

        .served-item {
            display: flex; align-items: center; justify-content: space-between; gap: 10px;
            padding: 11px 13px;
            border-radius: 14px;
            background: rgba(255, 255, 255, .05);
            border: 1px solid var(--line);
            border-left: 4px solid var(--sc, #64748b);
            animation: slideIn .35s ease;
        }
        @keyframes slideIn { from { opacity: 0; transform: translateX(14px); } }

        .served-main { display: flex; align-items: baseline; gap: 8px; min-width: 0; }
        .served-main .no { font-size: 1.3rem; font-weight: 800; color: #fff; font-variant-numeric: tabular-nums; }
        .served-main .poli { font-size: .74rem; color: #93a8c6; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .served-side { display: flex; align-items: center; gap: 6px; flex-shrink: 0; }

        .served-loket {
            display: inline-flex; align-items: center; gap: 5px;
            max-width: 130px;
            font-size: .7rem; font-weight: 700;
            padding: 3px 10px; border-radius: 20px;
            background: rgba(56, 189, 248, .16);
            color: #7dd3fc;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .served-loket.none { background: rgba(148, 163, 184, .14); color: #94a3b8; }

        .badge-status {
            font-size: .68rem; font-weight: 800;
            letter-spacing: .5px; text-transform: uppercase;
            padding: 3px 10px; border-radius: 20px;
        }

        .empty-state {
            text-align: center;
            color: #6f819b;
            font-size: .9rem;
            padding: 36px 10px;
        }
        .empty-state i { font-size: 2.4rem; display: block; margin-bottom: 10px; opacity: .5; }

        /* ============ MARQUEE ============ */
        .marquee-wrap {
            position: relative;
            overflow: hidden;
            padding: 11px 0;
            background: linear-gradient(90deg, rgba(56, 189, 248, .18), rgba(99, 102, 241, .18));
            border-top: 1px solid var(--line);
            white-space: nowrap;
        }
        .marquee-wrap::before, .marquee-wrap::after {
            content: ""; position: absolute; top: 0; bottom: 0; width: 140px; z-index: 2; pointer-events: none;
        }
        .marquee-wrap::before { left: 0; background: linear-gradient(90deg, var(--bg-deep), transparent); }
        .marquee-wrap::after { right: 0; background: linear-gradient(270deg, var(--bg-deep), transparent); }
        .marquee-wrap span {
            display: inline-block;
            padding-left: 100%;
            font-weight: 700; font-size: 1.08rem;
            color: #dbeafe;
            animation: marquee 28s linear infinite;
        }
        @keyframes marquee { from { transform: translateX(0); } to { transform: translateX(-100%); } }

        /* ============ OVERLAY ============ */
        .announce-pill {
            position: fixed; top: 96px; right: 28px; z-index: 1200;
            display: flex; align-items: center; gap: 10px;
            padding: 13px 24px;
            border-radius: 40px;
            font-weight: 800; font-size: 1.1rem;
            color: #04121f;
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            box-shadow: 0 12px 32px rgba(56, 189, 248, .38);
            border: 1px solid rgba(255, 255, 255, .35);
            animation: pop .3s ease;
        }
        @keyframes pop { from { transform: translateY(-12px); opacity: 0; } }

        .unlock-overlay {
            position: fixed; inset: 0; z-index: 2000;
            display: flex; align-items: center; justify-content: center;
            background: rgba(4, 10, 24, .9);
            backdrop-filter: blur(6px);
        }
        .unlock-box {
            max-width: 480px; text-align: center;
            padding: 40px 34px;
            border-radius: 26px;
            background: rgba(255, 255, 255, .06);
            border: 1px solid var(--line);
            box-shadow: 0 24px 60px rgba(0, 0, 0, .5);
        }
        .unlock-box .ico {
            width: 92px; height: 92px; margin: 0 auto 18px;
            display: grid; place-items: center;
            border-radius: 50%; font-size: 2.6rem; color: #04121f;
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            box-shadow: 0 0 0 8px rgba(56, 189, 248, .14);
        }
        .unlock-box h2 { font-size: 1.6rem; font-weight: 800; color: #fff; }
        .unlock-box p { color: #a8bcd8; font-size: .95rem; margin-top: 8px; }
        .unlock-box button {
            margin-top: 22px; padding: 14px 34px;
            border: 0; border-radius: 40px;
            font-size: 1.05rem; font-weight: 800; color: #04121f;
            cursor: pointer;
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            box-shadow: 0 12px 30px rgba(56, 189, 248, .32);
            transition: .2s;
        }
        .unlock-box button:hover { transform: translateY(-2px); }

        /* scrollbar */
        .history::-webkit-scrollbar, .loket-wrap::-webkit-scrollbar { width: 8px; }
        .history::-webkit-scrollbar-thumb, .loket-wrap::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, .16); border-radius: 8px;
        }
        .history::-webkit-scrollbar-track, .loket-wrap::-webkit-scrollbar-track { background: transparent; }

        @media (max-width: 1100px) {
            .main { grid-template-columns: 1fr; }
            .right { display: none; }
            .brand-title { font-size: 1.5rem; }
            .clock .waktu { font-size: 2rem; }
            .stat-row { grid-template-columns: repeat(3, 1fr); }
            .kiosk-btn { grid-column: 1 / -1; padding: 12px 22px; justify-content: center; }
        }
    </style>
</head>
<body>
<div class="app">
    <header class="header">
        <div class="brand">
            <div class="brand-mark"><i class="bi bi-hospital-fill"></i></div>
            <div class="brand-text">
                <div class="brand-eyebrow">Layar Panggilan Antrian</div>
                <div class="brand-title">{{ $namaPuskesmas }}</div>
            </div>
        </div>
        <div class="header-right">
            <button id="muteBtn" class="sound-btn" onclick="toggleMute()" title="Aktifkan / matikan suara">
                <i class="bi bi-volume-up" id="muteIcon"></i>
            </button>
            <div class="clock">
                <div class="tanggal" id="tanggal"></div>
                <div class="waktu" id="jam"></div>
            </div>
        </div>
    </header>

    <main class="main">
        <div class="left">
            <div class="panel stat-row">
                <div class="stat" style="--sc: var(--warn)">
                    <div class="ico"><i class="bi bi-megaphone-fill"></i></div>
                    <div>
                        <div class="val" id="statCalling">0</div>
                        <div class="lbl">Sedang Dipanggil</div>
                    </div>
                </div>
                <div class="stat" style="--sc: var(--ok)">
                    <div class="ico"><i class="bi bi-check-circle-fill"></i></div>
                    <div>
                        <div class="val" id="statDone">0</div>
                        <div class="lbl">Selesai</div>
                    </div>
                </div>
                <div class="stat" style="--sc: var(--accent)">
                    <div class="ico"><i class="bi bi-hourglass-split"></i></div>
                    <div>
                        <div class="val" id="statWaiting">0</div>
                        <div class="lbl">Menunggu</div>
                    </div>
                </div>
                <a href="{{ route('kiosk.index') }}" target="_blank" class="kiosk-btn">
                    <i class="bi bi-ticket-perforated-fill fs-3"></i>
                    <span>Ambil Antrian<span class="sub">Kiosk Mandiri</span></span>
                </a>
            </div>

            <div class="loket-wrap">
                <div class="loket-grid" id="loketGrid"></div>
            </div>
        </div>

        <aside class="panel right">
            <div class="right-head">
                <i class="bi bi-clock-history"></i>
                <h4>Nomor Terpanggil</h4>
            </div>
            <div class="history" id="lastServed"></div>
        </aside>
    </main>

    <div class="marquee-wrap">
        <span>{{ $marqueeText ?: 'Selamat datang di pelayanan kesehatan kami.' }}</span>
    </div>
</div>

<div id="announcePill" class="announce-pill d-none">
    <i class="bi bi-broadcast"></i> <span id="announceText"></span>
</div>

<div id="unlockOverlay" class="unlock-overlay">
    <div class="unlock-box">
        <div class="ico"><i class="bi bi-volume-up-fill"></i></div>
        <h2>Tekan untuk Mengaktifkan Suara</h2>
        <p>Nada panggilan dan pesan suara antrian baru aktif setelah diklik.</p>
        <button onclick="unlockAudio()"><i class="bi bi-unlock-fill"></i> Aktifkan Suara</button>
    </div>
</div>

<script>
    const loketGrid = document.getElementById('loketGrid');
    const lastServed = document.getElementById('lastServed');
    const announcePill = document.getElementById('announcePill');
    const announceText = document.getElementById('announceText');
    const muteIcon = document.getElementById('muteIcon');
    const muteBtn = document.getElementById('muteBtn');
    const unlockOverlay = document.getElementById('unlockOverlay');

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

    function esc(str) {
        return String(str == null ? '' : str).replace(/[&<>"']/g, c => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        }[c]));
    }

    function effectiveActive() {
        return !localMute && voiceCfg.active;
    }

    function updateMuteIcon() {
        const on = effectiveActive();
        muteIcon.className = on ? 'bi bi-volume-up-fill' : 'bi bi-volume-mute-fill';
        muteBtn.classList.toggle('muted', !on);
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
        unlockOverlay.style.display = 'none';
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
        if (!voiceCfg.active) unlockOverlay.style.display = 'none';
        updateMuteIcon();

        let calling = 0, done = 0, waiting = 0;
        let html = '';
        const newAnnouncements = [];

        data.lokets.forEach(l => {
            const warna = l.warna || '#64748b';
            const stateClass = l.is_calling ? 'calling' : 'idle';
            const angka = l.nomor_dipanggil ? l.nomor_dipanggil : '--';

            if (l.is_calling) calling++;
            else if (l.nomor_dipanggil) waiting++;

            html += `
                <div class="loket-card ${stateClass}" style="--c: ${esc(warna)}">
                    <div class="name">${esc(l.nama)}</div>
                    ${l.poli ? `<div class="poli">${esc(l.poli)}</div>` : ''}
                    <div class="angka">${esc(angka)}</div>
                    <div class="status">
                        ${l.is_calling ? '<i class="bi bi-broadcast-pin"></i> Sedang Dipanggil' : '<i class="bi bi-hourglass"></i> Menunggu'}
                    </div>
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

        document.getElementById('statCalling').textContent = calling;
        document.getElementById('statDone').textContent =
            typeof data.total_done === 'number' ? data.total_done : done;
        document.getElementById('statWaiting').textContent =
            typeof data.total_waiting === 'number' ? data.total_waiting : waiting;

        const badgeMap = {
            done:    { cls: 'ok',  label: 'Selesai' },
            skipped: { cls: 'warn',label: 'Lewat' },
            calling: { cls: 'info',label: 'Dipanggil' }
        };
        const badgeStyle = {
            ok:   'background:rgba(34,197,94,.16);color:#4ade80',
            warn: 'background:rgba(251,191,36,.16);color:#fcd34d',
            info: 'background:rgba(56,189,248,.16);color:#7dd3fc'
        };
        const dotStyle = {
            done: 'var(--ok)', skipped: 'var(--warn)', calling: 'var(--accent)'
        };

        let served = '';
        (data.last_served || []).forEach(s => {
            const b = badgeMap[s.status] || badgeMap.calling;
            const loketLabel = s.loket_label || s.loket;
            const loket = s.loket
                ? `<span class="served-loket" title="${esc(s.loket)}"><i class="bi bi-shop-window"></i> ${esc(loketLabel)}</span>`
                : '<span class="served-loket none"><i class="bi bi-shop-window"></i> -</span>';
            served += `<div class="served-item" style="--sc: ${esc(dotStyle[s.status] || 'var(--accent)')}">
                    <span class="served-main">
                        <span class="no">${esc(s.no_antrian)}</span>
                        <span class="poli">${esc(s.nama_poli || '')}</span>
                    </span>
                    <span class="served-side">
                        ${loket}
                        <span class="badge-status" style="${badgeStyle[b.cls]}">${b.label}</span>
                    </span>
                </div>`;
        });

        lastServed.innerHTML = served
            || '<div class="empty-state"><i class="bi bi-inbox"></i>Belum ada antrian dipanggil</div>';
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

    updateMuteIcon();
    updateClock();
    setInterval(updateClock, 1000);
    load();
    setInterval(load, 2000);
</script>
</body>
</html>
