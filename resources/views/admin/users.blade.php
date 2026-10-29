@extends('layouts.app')

@section('title', 'Kelola Petugas')
@section('pageTitle', 'Kelola Petugas')
@section('pageSubtitle', 'Akun petugas loket dan administrator')

@section('content')
@if(session('api_token'))
<div class="alert alert-success alert-dismissible fade show">
    <div class="fw-bold mb-1"><i class="bi bi-key-fill"></i> Token API untuk {{ session('api_token_user') }}</div>
    <p class="mb-2">Salin token di bawah ini dan simpan di tempat aman. Token hanya ditampilkan sekali ini.</p>
    <div class="input-group mb-2">
        <input type="text" class="form-control font-monospace" id="apiTokenBox" value="{{ session('api_token') }}" readonly>
        <button class="btn btn-outline-primary" type="button" onclick="copyToken()"><i class="bi bi-clipboard"></i> Salin</button>
    </div>
</div>
@endif
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-bold">Tambah Petugas</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.user.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" minlength="6" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Peran</label>
                        <select name="role" class="form-select" onchange="document.getElementById('loketWrap').style.display = this.value === 'loket' ? 'block' : 'none';">
                            <option value="loket">Petugas Loket</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>
                    <div class="mb-3" id="loketWrap">
                        <label class="form-label">Loket</label>
                        <select name="loket_id" class="form-select">
                            <option value="">-- Tanpa Loket --</option>
                            @foreach($lokets as $loket)
                                <option value="{{ $loket->id }}">{{ $loket->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="all_clusters" value="1" id="allClusters">
                        <label class="form-check-label" for="allClusters">Akses semua klaster</label>
                    </div>
                    <button class="btn btn-primary w-100">Simpan</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-bold">Daftar Petugas</div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Peran</th>
                            <th>Loket</th>
                            <th>Token API</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td class="fw-bold">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->isAdmin())
                                    <span class="badge bg-primary">Admin</span>
                                @else
                                    <span class="badge bg-secondary">Loket</span>
                                @endif
                            </td>
                            <td>
                                @if($user->canAccessAllClusters())
                                    <span class="badge bg-info text-dark">Semua klaster</span>
                                @else
                                    {{ $user->loket?->nama ?? '-' }}
                                @endif
                            </td>
                            <td>
                                @if($user->isLoket() && ($user->canAccessAllClusters() || $user->loket_id))
                                    @if($user->api_token)
                                        <span class="badge bg-success" title="{{ $user->api_token }}">Aktif</span>
                                    @else
                                        <span class="badge bg-danger">Tanpa Token</span>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($user->isLoket() && ($user->canAccessAllClusters() || $user->loket_id))
                                    <form action="{{ route('admin.user.token', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-primary" {{ $user->api_token ? 'onclick="return confirm(\'Buat token baru? Token lama akan tidak berlaku.\')"' : '' }}>
                                            <i class="bi bi-key"></i> {{ $user->api_token ? 'Atur Ulang' : 'Buat Token' }}
                                        </button>
                                    </form>
                                    @if($user->api_token)
                                        <form action="{{ route('admin.user.token.revoke', $user) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Nonaktifkan token API ini?')">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-secondary" title="Nonaktifkan">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>
                                    @endif
                                @endif
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#edit{{ $user->id }}">Edit</button>
                                <form action="{{ route('admin.user.destroy', $user) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus petugas ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>

                        <div class="modal fade" id="edit{{ $user->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST" action="{{ route('admin.user.update', $user) }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit {{ $user->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Nama</label>
                                                <input type="text" name="name" value="{{ $user->name }}" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Email</label>
                                                <input type="email" name="email" value="{{ $user->email }}" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Password (kosongkan jika tidak diganti)</label>
                                                <input type="password" name="password" class="form-control" minlength="6">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Peran</label>
                                                <select name="role" class="form-select">
                                                    <option value="loket" {{ $user->role === 'loket' ? 'selected' : '' }}>Petugas Loket</option>
                                                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Administrator</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Loket</label>
                                                <select name="loket_id" class="form-select">
                                                    <option value="">-- Tanpa Loket --</option>
                                                    @foreach($lokets as $loket)
                                                        <option value="{{ $loket->id }}" {{ $user->loket_id == $loket->id ? 'selected' : '' }}>{{ $loket->nama }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox" name="all_clusters" value="1" id="allClusters{{ $user->id }}" {{ $user->all_clusters ? 'checked' : '' }}>
                                                <label class="form-check-label" for="allClusters{{ $user->id }}">Akses semua klaster</label>
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

@push('scripts')
<script>
    function copyToken() {
        const box = document.getElementById('apiTokenBox');
        box.select();
        box.setSelectionRange(0, 99999);
        try { navigator.clipboard.writeText(box.value); } catch (e) { document.execCommand('copy'); }
        alert('Token berhasil disalin.');
    }
</script>
@endpush