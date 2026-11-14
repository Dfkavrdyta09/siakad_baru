@extends('layouts.app')

@section('title', 'Detail Mata Kuliah')

@section('content')
    <x-page-header judul="Detail Mata Kuliah"
        deskripsi="Informasi mata kuliah dan KRS yang memakainya.">
        <a href="{{ route('matakuliah.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        @if (Auth::user()->role === 'admin')
            <a href="{{ route('matakuliah.edit', $mataKuliah) }}" class="btn btn-warning">
                <i class="bi bi-pencil-square"></i> Ubah
            </a>
        @endif
    </x-page-header>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-body fw-semibold">{{ $mataKuliah->nama_mk }}</div>
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Kode</dt>
                <dd class="col-sm-9">{{ $mataKuliah->kode_mk }}</dd>

                <dt class="col-sm-3">Nama</dt>
                <dd class="col-sm-9">{{ $mataKuliah->nama_mk }}</dd>

                <dt class="col-sm-3">SKS</dt>
                <dd class="col-sm-9">{{ $mataKuliah->sks }}</dd>
            </dl>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-body fw-semibold">Dipakai pada KRS</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>NIM</th>
                            <th>Mahasiswa</th>
                            <th>Tahun</th>
                            <th>Semester</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($mataKuliah->krsDetail as $detail)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $detail->krs?->mahasiswa?->nim ?? '-' }}</td>
                                <td>{{ $detail->krs?->mahasiswa?->nama ?? '-' }}</td>
                                <td>{{ $detail->krs?->tahun_akademik ?? '-' }}</td>
                                <td>{{ $detail->krs?->semester ?? '-' }}</td>
                                <td>
                                    @if ($detail->krs)
                                        <a href="{{ route('krs.show', $detail->krs) }}"
                                            class="btn btn-sm btn-outline-info">Detail</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-body-secondary py-4">
                                    Belum dipakai pada KRS.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
