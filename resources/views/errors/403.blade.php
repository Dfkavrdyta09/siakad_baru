@extends('layouts.app')

@section('title', 'Akses Ditolak')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="bi bi-shield-lock-fill display-4 text-danger"></i>
            <h1 class="h3 mt-3 mb-2">403 - Akses Ditolak</h1>
            <p class="text-body-secondary mb-4">
                {{ $exception->getMessage() ?: 'Anda tidak berhak mengakses halaman ini.' }}
            </p>
            <a href="{{ route('dashboard') }}" class="btn btn-primary">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>
@endsection
