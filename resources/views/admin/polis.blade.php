@extends('layouts.app')

@section('title', 'Kelola Layanan')
@section('pageTitle', 'Kelola Layanan')
@section('pageSubtitle', 'Layanan per klaster untuk pengambilan antrian')

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-bold">Tambah Layanan</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.polis.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Klaster</label>
                        <select name="klaster_id" class="form-select" required>
                            <option value="">-- Pilih Klaster --</option>
                            @foreach($klasters as $klaster)
                                <option value="{{ $klaster->id }}">{{ $klaster->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Layanan</label>
                        <input type="text" name="nama" class="form-control" placeholder="KIA" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kode Huruf</label>
                        <input type="text" name="prefix" class="form-control" maxlength="4" placeholder="A" required>
                        <div class="form-text">Format nomor antrian, misal: A (A001)</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Warna</label>
                        <input type="color" name="warna" class="form-control form-control-color" value="#0d6efd">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="2"></textarea>
                    </div>
                    <button class="btn btn-primary w-100">Simpan</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-bold">Daftar Layanan</div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Layanan</th>
                            <th>Klaster</th>
                            <th>Kode</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($polis as $poli)
                        <tr>
                            <td>
                                <span class="d-inline-block me-2" style="width:14px;height:14px;border-radius:4px;background:{{ $poli->warna }}"></span>
                                <strong>{{ $poli->nama }}</strong>
                                <div class="text-muted small">{{ $poli->deskripsi }}</div>
                            </td>
                            <td>{{ $poli->klaster?->nama }}</td>
                            <td><span class="badge bg-secondary fs-6">{{ $poli->prefix }}</span></td>
                            <td>
                                @if($poli->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#edit{{ $poli->id }}">Edit</button>
                                <form action="{{ route('admin.polis.destroy', $poli) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus layanan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>

                        <div class="modal fade" id="edit{{ $poli->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST" action="{{ route('admin.polis.update', $poli) }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit {{ $poli->nama }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Klaster</label>
                                                <select name="klaster_id" class="form-select" required>
                                                    @foreach($klasters as $klaster)
                                                        <option value="{{ $klaster->id }}" {{ $poli->klaster_id == $klaster->id ? 'selected' : '' }}>
                                                            {{ $klaster->nama }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Nama Layanan</label>
                                                <input type="text" name="nama" value="{{ $poli->nama }}" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Kode Huruf</label>
                                                <input type="text" name="prefix" value="{{ $poli->prefix }}" class="form-control" maxlength="4" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Warna</label>
                                                <input type="color" name="warna" value="{{ $poli->warna }}" class="form-control form-control-color">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Deskripsi</label>
                                                <textarea name="deskripsi" class="form-control" rows="2">{{ $poli->deskripsi }}</textarea>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input type="checkbox" name="is_active" value="1" class="form-check-input" id="aktif{{ $poli->id }}" {{ $poli->is_active ? 'checked' : '' }}>
                                                <label class="form-check-label" for="aktif{{ $poli->id }}">Aktif</label>
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