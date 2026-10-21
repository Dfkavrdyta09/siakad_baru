@extends('layouts.app')

@section('title', 'Detail Mahasiswa')

@section('content')
    <x-page-header judul="Detail Mahasiswa"
        deskripsi="Informasi data mahasiswa.">
        <a href="{{ route('mahasiswa.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </x-page-header>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-body fw-semibold">Data Mahasiswa</div>
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">NIM</dt>
                <dd class="col-sm-9">{{ $mahasiswa['nim'] }}</dd>

                <dt class="col-sm-3">Nama Lengkap</dt>
                <dd class="col-sm-9">{{ $mahasiswa['nama'] }}</dd>

                <dt class="col-sm-3">Angkatan</dt>
                <dd class="col-sm-9">{{ $mahasiswa['angkatan'] }}</dd>

                <dt class="col-sm-3">Email</dt>
                <dd class="col-sm-9">{{ $mahasiswa['email'] }}</dd>

                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9">
                    @php
                        $warna = match ($mahasiswa['status']) {
                            'Aktif' => 'success',
                            'Cuti' => 'warning',
                            default => 'secondary',
                        };
                    @endphp
                    <span class="badge text-bg-{{ $warna }}">
                        {{ $mahasiswa['status'] }}
                    </span>
                </dd>
            </dl>
        </div>
    </div>
@endsection
