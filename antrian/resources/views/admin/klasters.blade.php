@extends('layouts.app')

@section('title', 'Kelola Klaster')
@section('pageTitle', 'Kelola Klaster')
@section('pageSubtitle', 'Pengelompokan layanan sesuai panduan klaster puskesmas')

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-bold">Tambah Klaster</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.klaster.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nama Klaster</label>
                        <input type="text" name="nama" class="form-control" placeholder="Klaster 1 (Administrasi)" required>
                        <div class="form-text">Contoh: Klaster 2 (KIA, Remaja, Imunisasi)</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Warna</label>
                        <input type="color" name="warna" class="form-control form-control-color" value="#0d6efd">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Urutan</label>
                        <input type="number" name="urutan" class="form-control" value="1" min="0" required>
                        <div class="form-text">Semakin kecil, semakin atas di kiosk</div>
                    </div>
                    <button class="btn btn-primary w-100">Simpan</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-bold">Daftar Klaster</div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Urutan</th>
                            <th>Klaster</th>
                            <th class="text-center">Jumlah Layanan</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($klasters as $klaster)
                        <tr>
                            <td>{{ $klaster->urutan }}</td>
                            <td>
                                <span class="d-inline-block me-2" style="width:14px;height:14px;border-radius:4px;background:{{ $klaster->warna }}"></span>
                                <strong>{{ $klaster->nama }}</strong>
                            </td>
                            <td class="text-center"><span class="badge bg-light text-dark">{{ $klaster->polis_count }}</span></td>
                            <td>
                                @if($klaster->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#edit{{ $klaster->id }}">Edit</button>
                                <form action="{{ route('admin.klaster.destroy', $klaster) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus klaster ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>

                        <div class="modal fade" id="edit{{ $klaster->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST" action="{{ route('admin.klaster.update', $klaster) }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Klaster</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Nama Klaster</label>
                                                <input type="text" name="nama" value="{{ $klaster->nama }}" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Warna</label>
                                                <input type="color" name="warna" value="{{ $klaster->warna }}" class="form-control form-control-color">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Urutan</label>
                                                <input type="number" name="urutan" value="{{ $klaster->urutan }}" class="form-control" min="0" required>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input type="checkbox" name="is_active" value="1" class="form-check-input" id="aktif{{ $klaster->id }}" {{ $klaster->is_active ? 'checked' : '' }}>
                                                <label class="form-check-label" for="aktif{{ $klaster->id }}">Aktif</label>
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