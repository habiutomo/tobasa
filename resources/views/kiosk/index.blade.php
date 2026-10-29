<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ambil Antrian - {{ $namaPuskesmas }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #eef4ff 0%, #dce9ff 100%);
            font-family: "Segoe UI", system-ui, sans-serif;
        }
        .header {
            background: linear-gradient(90deg, #0d6efd, #0a4fb0);
            color: #fff;
            padding: 22px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 4px solid #ffc107;
        }
        .header .title { font-size: 2rem; font-weight: 800; }
        .header .jam { font-size: 1.6rem; font-weight: 700; }
        .klaster-btn {
            border: 4px solid #fff;
            border-radius: 20px;
            padding: 24px 20px;
            color: #fff;
            font-weight: 800;
            font-size: 1.1rem;
            min-height: 160px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: transform .15s ease, box-shadow .15s ease;
            cursor: pointer;
            box-shadow: 0 8px 16px rgba(0,0,0,.15);
            width: 100%;
            text-align: center;
        }
        .klaster-btn:hover { transform: translateY(-4px) scale(1.02); box-shadow: 0 12px 24px rgba(0,0,0,.25); }
        .klaster-btn .icon { font-size: 2.2rem; }
        .klaster-btn .sub { font-size: .85rem; font-weight: 600; opacity: .9; }

        .service-btn {
            width: 100%;
            border-radius: 16px;
            padding: 20px 16px;
            color: #fff;
            font-weight: 700;
            font-size: 1.1rem;
            border: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 6px 14px rgba(0,0,0,.15);
            transition: transform .12s ease;
        }
        .service-btn:hover { transform: scale(1.03); }
        .service-btn .no { font-weight: 900; font-size: 1.3rem; }

        .ticket {
            background: #fff;
            border-radius: 14px;
            padding: 26px;
            max-width: 360px;
            margin: 0 auto;
            border: 1px dashed #0d6efd;
            text-align: center;
        }
        .ticket .no-antrian { font-size: 3.4rem; font-weight: 900; color: #0d6efd; letter-spacing: 4px; }
        .modal-backdrop-ticket { position: fixed; inset: 0; background: rgba(0,0,0,.55); z-index: 1050; display: flex; align-items: center; justify-content: center; }
        .modal-backdrop-service {
            position: fixed; inset: 0; background: rgba(10,30,60,.72); z-index: 1040;
            display: none; align-items: center; justify-content: center; overflow-y: auto; padding: 24px;
        }
        .service-panel {
            background: #fff; border-radius: 20px; padding: 26px; width: 100%; max-width: 560px;
            box-shadow: 0 20px 40px rgba(0,0,0,.3);
        }
        .print-area { display: none; }
        @media print {
            @page { size: 58mm 70mm; margin: 0; }
            html, body {
                margin: 0 !important; padding: 0 !important;
                height: 70mm !important; overflow: hidden !important;
            }
            body > * { display: none !important; }
            body > #printArea {
                display: block !important;
                position: absolute;
                left: 0;
                top: 0;
                width: 58mm !important;
                height: 70mm !important;
                max-height: 70mm !important;
                overflow: hidden !important;
                page-break-inside: avoid !important;
                page-break-after: avoid !important;
                page-break-before: avoid !important;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title"><i class="bi bi-hospital"></i> {{ $namaPuskesmas }}</div>
        <div class="text-center">
            <div class="jam time">{{ now()->format('H:i') }}</div>
            <div class="small opacity-75">Buka {{ $jamBuka }} - {{ $jamTutup }} WIB</div>
        </div>
    </div>

    <div class="container py-4">
        @if($kioskOpen)
        <div class="text-center mb-4">
            <h1 class="h3 fw-bold text-primary">Silakan Ambil Nomor Antrian</h1>
            <p class="text-muted">Pilih klaster layanan yang sesuai dengan kebutuhan Anda</p>
        </div>
        <div class="row" id="klasterGrid"></div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-clock-history text-warning" style="font-size: 4rem;"></i>
            <h2 class="fw-bold mt-3">Pengambilan Antrian Sedang Ditutup</h2>
            <p class="text-muted">Silakan kembali pada jam layanan ({{ $jamBuka }} - {{ $jamTutup }} WIB)</p>
        </div>
        @endif
    </div>

    {{-- Modal pilih layanan --}}
    <div class="modal-backdrop-service" id="serviceModal" onclick="if(event.target===this)closeService()">
        <div class="service-panel">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-bold mb-0" id="serviceTitle">Pilih Layanan</h4>
                <button class="btn-close" onclick="closeService()"></button>
            </div>
            <div id="serviceList"></div>
        </div>
    </div>

    {{-- Modal tiket --}}
    <div id="ticketModal" class="modal-backdrop-ticket" style="display:none;">
        <div class="card text-center p-4" style="max-width: 400px; width: 100%;">
            <div class="mb-2 text-success fs-1"><i class="bi bi-check-circle-fill"></i></div>
            <h4 class="fw-bold text-success">Berhasil!</h4>
            <p class="mb-1">Nomor antrian Anda :</p>
            <div class="display-1 fw-bold text-primary" id="ticketNo"></div>
            <div class="fs-5" id="ticketPoli"></div>
            <div class="text-muted" id="ticketWaiting"></div>
            <div class="mt-3 d-grid gap-2">
                <button class="btn btn-primary btn-lg" onclick="printTicket()"><i class="bi bi-printer"></i> Cetak Tiket</button>
                <button class="btn btn-outline-secondary" onclick="closeTicket()">Selesai</button>
            </div>
        </div>
    </div>

    <div class="print-area" id="printArea"></div>

    <script>
        const grid = document.getElementById('klasterGrid');
        const serviceModal = document.getElementById('serviceModal');
        let klastersCache = [];

        async function loadKlasters() {
            try {
                const res = await fetch('{{ route('kiosk.klasters') }}');
                const klasters = await res.json();
                klastersCache = klasters;
                let html = '';
                klasters.forEach(k => {
                    const listNama = k.polis.map(p => p.nama).join(' • ');
                    html += `
                        <div class="col-12 col-md-6 col-lg-4 mb-3">
                            <div class="klaster-btn" style="background:${k.warna};" onclick="openService(${k.id})">
                                <div class="icon"><i class="bi bi-layers"></i></div>
                                <div>${k.nama}</div>
                                <div class="sub">${listNama}</div>
                            </div>
                        </div>`;
                });
                grid.innerHTML = html;
            } catch (e) {
                grid.innerHTML = '<div class="text-center text-danger">Gagal memuat data klaster</div>';
            }
        }

        function openService(klasterId) {
            const k = klastersCache.find(x => x.id === klasterId);
            if (!k) return;
            document.getElementById('serviceTitle').textContent = k.nama;
            let html = '';
            k.polis.forEach(p => {
                html += `
                    <button class="service-btn mb-2" style="background:${p.warna};" onclick="ambilAntrian(${p.id})">
                        <span><i class="bi bi-bag-plus"></i> ${p.nama}</span>
                        <span class="no">${p.prefix}</span>
                    </button>`;
            });
            document.getElementById('serviceList').innerHTML = html || '<div class="text-muted text-center py-3">Belum ada layanan di klaster ini</div>';
            serviceModal.style.display = 'flex';
        }

        function closeService() {
            serviceModal.style.display = 'none';
        }

        let beepAudio = new Audio('data:audio/wav;base64,UklGRjQAAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YTAAAAAA');
        function beep() {
            try { beepAudio.currentTime = 0; beepAudio.play(); } catch (e) {}
        }

        async function ambilAntrian(poliId) {
            closeService();
            if (!confirm('Ambil nomor antrian untuk layanan ini?')) return;
            beep();
            try {
                const res = await fetch('{{ route('kiosk.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    },
                    body: JSON.stringify({ poli_id: poliId })
                });
                const data = await res.json();
                if (!res.ok) { alert(data.message || 'Gagal mengambil antrian'); return; }
                showTicket(data.antrian);
            } catch (e) {
                alert('Terjadi kesalahan, coba lagi.');
            }
        }

        function showTicket(a) {
            document.getElementById('ticketNo').textContent = a.no_antrian;
            document.getElementById('ticketPoli').textContent = a.nama_poli;
            document.getElementById('ticketWaiting').textContent = `Sisa antrian menunggu: ${a.sisa_menunggu}`;
            document.getElementById('ticketModal').style.display = 'flex';
            beep();
            beep();
            setTimeout(beep, 500);
            buildPrintArea(a);
            setTimeout(printTicket, 400);
        }

        function buildPrintArea(a) {
            document.getElementById('printArea').innerHTML = `
                <div style="font-family:Arial,sans-serif; text-align:center; width:100%; box-sizing:border-box; padding:3mm 3mm 0; color:#000;">
                    <h2 style="margin:0 0 2mm; font-size:17px; line-height:1.2; letter-spacing:.4px;">{{ $namaPuskesmas }}</h2>
                    <p style="margin:0 0 1mm; font-size:13px;">Nomor Antrian</p>
                    <div style="font-size:48px; font-weight:800; margin:0 0 2mm; line-height:1.05;">${a.no_antrian}</div>
                    <p style="margin:0 0 2mm; font-size:16px; line-height:1.2; font-weight:bold;">${a.nama_poli}</p>
                    <p style="margin:0 0 1.5mm; font-size:13px; line-height:1.2;">Sisa menunggu: ${a.sisa_menunggu}</p>
                    <p style="margin:0; font-size:12px; line-height:1.2;">${new Date().toLocaleString('id-ID')}</p>
                </div>`;
        }

        function printTicket() {
            window.print();
        }

        function closeTicket() {
            document.getElementById('ticketModal').style.display = 'none';
        }

        loadKlasters();

        setInterval(() => {
            document.querySelector('.header .time') &&
                (document.querySelector('.header .time').textContent = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }));
        }, 10000);
    </script>
</body>
</html>
