@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('pageTitle', 'Dashboard')
@section('pageSubtitle', 'Ringkasan antrian hari ini')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-6 col-md-2">
        <div class="card text-center p-3 shadow-sm">
            <div class="fs-1 fw-bold text-primary">{{ $totalHariIni }}</div>
            <div class="text-muted small">Total Antrian</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card text-center p-3 shadow-sm">
            <div class="fs-1 fw-bold text-info">{{ $menunggu }}</div>
            <div class="text-muted small">Menunggu</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card text-center p-3 shadow-sm">
            <div class="fs-1 fw-bold text-warning">{{ $dipanggil }}</div>
            <div class="text-muted small">Dipanggil</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card text-center p-3 shadow-sm">
            <div class="fs-1 fw-bold text-success">{{ $selesai }}</div>
            <div class="text-muted small">Selesai</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card text-center p-3 shadow-sm">
            <div class="fs-1 fw-bold text-danger">{{ $lewat }}</div>
            <div class="text-muted small">Lewat</div>
        </div>
    </div>
    <div class="col-6 col-md-2 d-grid">
        <a href="{{ route('display.index') }}" target="_blank" class="btn btn-outline-primary d-flex flex-column justify-content-center">
            <i class="bi bi-tv fs-4"></i> Layar Antrian
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-bold">Antrian Hari Ini</span>
        <button class="btn btn-outline-danger btn-sm" onclick="if(confirm('Yakin reset semua antrian hari ini?')) document.getElementById('reset-form').submit();">
            <i class="bi bi-arrow-counterclockwise"></i> Reset Antrian
        </button>
        <form id="reset-form" action="{{ route('admin.reset-hari-ini') }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>No. Antrian</th>
                    <th>Poli</th>
                    <th>Pasien</th>
                    <th>Loket</th>
                    <th>Status</th>
                    <th>Diambil</th>
                </tr>
            </thead>
            <tbody>
                @forelse($antrians as $a)
                <tr>
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
                    <td>{{ $a->created_at->format('H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Belum ada antrian hari ini</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection