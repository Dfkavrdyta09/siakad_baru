@extends('layouts.app')

@section('title', 'Detail KRS')

@php
    $statusWarna = [
        'draft' => 'secondary', 'diajukan' => 'info',
        'disetujui' => 'success', 'ditolak' => 'danger',
    ][$krs->status] ?? 'secondary';
    $diproses = in_array($krs->status, ['disetujui', 'ditolak'], true);
    $persen = $batasSks > 0 ? min(100, (int) round($terpakai / $batasSks * 100)) : 0;
@endphp

@section('content')
    <x-page-header judul="Detail KRS"
        deskripsi="Data KRS beserta daftar mata kuliah yang diambil.">
        <a href="{{ route('krs.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        @if (Auth::user()->role === 'admin')
            <a href="{{ route('krs.edit', $krs) }}" class="btn btn-warning">
                <i class="bi bi-pencil-square"></i> Ubah
            </a>
        @endif
    </x-page-header>

    <div class="row g-3">
        <div class="col-12 col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-body fw-semibold">Mata Kuliah Diambil</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Kode</th>
                                    <th>Mata Kuliah</th>
                                    <th>SKS</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($krs->krsDetail as $detail)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $detail->mataKuliah?->kode_mk ?? '-' }}</td>
                                        <td>{{ $detail->mataKuliah?->nama_mk ?? '-' }}</td>
                                        <td>{{ $detail->mataKuliah?->sks ?? 0 }}</td>
                                        <td>
                                            @if (! $diproses)
                                                <form method="POST"
                                                    action="{{ route('krs.detail.destroy', [$krs, $detail]) }}"
                                                    onsubmit="return confirm('Hapus mata kuliah ini dari KRS?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-trash"></i> Hapus
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-body-secondary small">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-body-secondary py-4">
                                            Belum ada mata kuliah.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-body fw-semibold">Informasi KRS</div>
                <div class="card-body">
                    <dl class="row mb-3">
                        <dt class="col-5">NIM</dt>
                        <dd class="col-7">{{ $krs->mahasiswa?->nim ?? '-' }}</dd>

                        <dt class="col-5">Mahasiswa</dt>
                        <dd class="col-7">{{ $krs->mahasiswa?->nama ?? '-' }}</dd>

                        <dt class="col-5">Tahun</dt>
                        <dd class="col-7">{{ $krs->tahun_akademik }}</dd>

                        <dt class="col-5">Semester</dt>
                        <dd class="col-7">{{ $krs->semester }}</dd>

                        <dt class="col-5">Status</dt>
                        <dd class="col-7">
                            <span class="badge text-bg-{{ $statusWarna }}">
                                {{ ucfirst($krs->status) }}
                            </span>
                        </dd>
                    </dl>

                    <div class="d-flex justify-content-between small mb-1">
                        <span>Beban SKS</span>
                        <span>{{ $terpakai }} / {{ $batasSks }} SKS</span>
                    </div>
                    <div class="progress" role="progressbar"
                        aria-valuenow="{{ $terpakai }}" aria-valuemin="0"
                        aria-valuemax="{{ $batasSks }}">
                        <div class="progress-bar bg-{{ $terpakai >= $batasSks ? 'danger' : 'success' }}"
                            style="width: {{ $persen }}%"></div>
                    </div>
                </div>
            </div>

            @if (! $diproses)
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-body fw-semibold">Tambah Mata Kuliah</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('krs.detail.store', $krs) }}">
                            @csrf
                            <select name="mata_kuliah_id"
                                @class(['form-select', 'is-invalid' => $errors->has('mata_kuliah_id')])
                                required>
                                <option value="">-- Pilih Mata Kuliah --</option>
                                @foreach ($daftarMataKuliah as $mk)
                                    <option value="{{ $mk->id }}"
                                        @selected(old('mata_kuliah_id') == $mk->id)>
                                        {{ $mk->kode_mk }} - {{ $mk->nama_mk }}
                                        ({{ $mk->sks }} SKS)
                                    </option>
                                @endforeach
                            </select>
                            @error('mata_kuliah_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <button type="submit" class="btn btn-primary mt-3">
                                <i class="bi bi-plus-lg"></i> Tambah
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-body fw-semibold">Ubah Status</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('krs.status', $krs) }}"
                        class="d-flex gap-2">
                        @csrf
                        @method('PATCH')
                        <select name="status" class="form-select form-select-sm">
                            @foreach (['draft' => 'Draft', 'diajukan' => 'Diajukan',
                                'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak'] as $nilai => $label)
                                <option value="{{ $nilai }}"
                                    @selected($krs->status === $nilai)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-sm btn-outline-primary">
                            Simpan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
