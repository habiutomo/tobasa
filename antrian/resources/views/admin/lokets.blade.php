@extends('layouts.app')

@section('title', 'Kelola Loket')
@section('pageTitle', 'Kelola Loket')
@section('pageSubtitle', 'Konfigurasi loket pelayanan')

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-bold">Tambah Loket</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.loket.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nama Loket</label>
                        <input type="text" name="nama" class="form-control" placeholder="Loket 1 - Umum" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Poli</label>
                        <select name="poli_id" class="form-select" required>
                            <option value="">-- Pilih Poli --</option>
                            @foreach($polis as $poli)
                                <option value="{{ $poli->id }}">{{ $poli->nama }} ({{ $poli->prefix }})</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-primary w-100">Simpan</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-bold">Daftar Loket</div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Loket</th>
                            <th>Poli</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lokets as $loket)
                        <tr>
                            <td class="fw-bold">{{ $loket->nama }}</td>
                            <td>{{ $loket->poli?->nama ?? '-' }}</td>
                            <td>
                                @if($loket->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#edit{{ $loket->id }}">Edit</button>
                                <form action="{{ route('admin.loket.destroy', $loket) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus loket ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>

                        <div class="modal fade" id="edit{{ $loket->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST" action="{{ route('admin.loket.update', $loket) }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit {{ $loket->nama }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Nama Loket</label>
                                                <input type="text" name="nama" value="{{ $loket->nama }}" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Poli</label>
                                                <select name="poli_id" class="form-select" required>
                                                    @foreach($polis as $poli)
                                                        <option value="{{ $poli->id }}" {{ $loket->poli_id == $poli->id ? 'selected' : '' }}>
                                                            {{ $poli->nama }} ({{ $poli->prefix }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input type="checkbox" name="is_active" value="1" class="form-check-input" id="aktif{{ $loket->id }}" {{ $loket->is_active ? 'checked' : '' }}>
                                                <label class="form-check-label" for="aktif{{ $loket->id }}">Aktif</label>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection