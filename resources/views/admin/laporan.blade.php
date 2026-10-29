@extends('layouts.app')

@section('title', 'Laporan Antrian')
@section('pageTitle', 'Laporan Antrian')
@section('pageSubtitle', 'Rekap jumlah pasien per poli')

@section('content')
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.laporan') }}" class="row g-3 align-items-end">
            <div class="col-auto">
                <label class="form-label small mb-1">Tanggal Awal</label>
                <input type="date" name="tanggal_awal" value="{{ $tanggalAwal }}" class="form-control">
            </div>
            <div class="col-auto">
                <label class="form-label small mb-1">Tanggal Akhir</label>
                <input type="date" name="tanggal_akhir" value="{{ $tanggalAkhir }}" class="form-control">
            </div>
            <div class="col-auto">
                <button class="btn btn-primary"><i class="bi bi-funnel"></i> Filter</button>
            </div>
            <div class="col-auto ms-auto d-flex gap-2">
                <button type="button" class="btn btn-outline-success" onclick="window.print()"><i class="bi bi-printer"></i> Cetak</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card text-center p-3 shadow-sm">
            <div class="fs-1 fw-bold">{{ $total }}</div>
            <div class="text-muted small">Total Antrian</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center p-3 shadow-sm">
            <div class="fs-1 fw-bold text-success">{{ $totalSelesai }}</div>
            <div class="text-muted small">Selesai Dilayani</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center p-3 shadow-sm">
            <div class="fs-1 fw-bold text-danger">{{ $totalLewat }}</div>
            <div class="text-muted small">Tidak Hadir / Lewat</div>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white fw-bold">Rekap per Poli</div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Poli</th>
                    <th class="text-center">Total</th>
                    <th class="text-center">Menunggu</th>
                    <th class="text-center">Dipanggil</th>
                    <th class="text-center">Selesai</th>
                    <th class="text-center">Lewat</th>
                </tr>
            </thead>
            <tbody>
                @foreach($perPoli as $p)
                <tr>
                    <td class="fw-bold">{{ $p['poli'] }}</td>
                    <td class="text-center">{{ $p['total'] }}</td>
                    <td class="text-center text-info">{{ $p['waiting'] }}</td>
                    <td class="text-center text-warning">{{ $p['dipanggil'] }}</td>
                    <td class="text-center text-success">{{ $p['selesai'] }}</td>
                    <td class="text-center text-danger">{{ $p['lewat'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white fw-bold">Detail Antrian</div>
    <div class="table-responsive" style="max-height:480px; overflow-y:auto;">
        <table class="table table-sm table-hover align-middle mb-0">
            <thead class="table-light sticky-top">
                <tr>
                    <th>Tanggal</th>
                    <th>No. Antrian</th>
                    <th>Poli</th>
                    <th>Pasien</th>
                    <th>Loket</th>
                    <th>Status</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
                @forelse($antrians as $a)
                <tr>
                    <td>{{ $a->tanggal->format('d M Y') }}</td>
                    <td class="fw-bold">{{ $a->no_antrian }}</td>
                    <td>{{ $a->poli?->nama }}</td>
                    <td>{{ $a->nama_pasien ?: '-' }}</td>
                    <td>{{ $a->loket?->nama ?: '-' }}</td>
                    <td>
                        @if($a->status === 'waiting')
                            <span class="badge bg-info">Menunggu</span>
                        @elseif($a->status === 'calling')
                            <span class="badge bg-warning text-dark">Dipanggil</span>
                        @elseif($a->status === 'done')
                            <span class="badge bg-success">Selesai</span>
                        @else
                            <span class="badge bg-danger">Lewat</span>
                        @endif
                    </td>
                    <td>{{ $a->created_at->format('H:i') }} - {{ optional($a->finished_at)->format('H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada data pada rentang tanggal tersebut</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    // Penyesuaian tampilan saat dicetak
    const style = document.createElement('style');
    style.textContent = '@media print { .sidebar, .d-flex.justify-content-between, form, .btn { display: none !important; } .col-lg-7, .col { padding: 0 !important; } }';
    document.head.appendChild(style);
</script>
@endsection