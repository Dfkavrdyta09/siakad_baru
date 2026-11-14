@extends('layouts.app')

@section('title', 'Detail Mahasiswa')

@section('content')
    <x-page-header judul="Detail Mahasiswa"
        deskripsi="Informasi data mahasiswa dan riwayat KRS.">
        <a href="{{ route('mahasiswa.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        @if (Auth::user()->role === 'admin')
            <a href="{{ route('mahasiswa.edit', $mahasiswa) }}" class="btn btn-warning">
                <i class="bi bi-pencil-square"></i> Ubah
            </a>
        @endif
    </x-page-header>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-body fw-semibold">{{ $mahasiswa->nama }}</div>
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">NIM</dt>
                <dd class="col-sm-9">{{ $mahasiswa->nim }}</dd>

                <dt class="col-sm-3">Nama Lengkap</dt>
                <dd class="col-sm-9">{{ $mahasiswa->nama }}</dd>

                <dt class="col-sm-3">Jenis Kelamin</dt>
                <dd class="col-sm-9">
                    {{ $mahasiswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                </dd>

                <dt class="col-sm-3">Email</dt>
                <dd class="col-sm-9">{{ $mahasiswa->email }}</dd>

                <dt class="col-sm-3">Angkatan</dt>
                <dd class="col-sm-9">{{ $mahasiswa->angkatan }}</dd>

                <dt class="col-sm-3">Program Studi</dt>
                <dd class="col-sm-9">{{ $mahasiswa->program_studi }}</dd>
            </dl>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-body fw-semibold">Riwayat KRS</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Tahun Akademik</th>
                            <th>Semester</th>
                            <th>Total SKS</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($mahasiswa->krs as $krs)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $krs->tahun_akademik }}</td>
                                <td>{{ $krs->semester }}</td>
                                <td>{{ $krs->mataKuliah->sum('sks') }}</td>
                                <td><span class="badge text-bg-secondary">{{ ucfirst($krs->status) }}</span></td>
                                <td>
                                    <a href="{{ route('krs.show', $krs) }}"
                                        class="btn btn-sm btn-outline-info">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-body-secondary py-4">
                                    Belum ada KRS.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
