@extends('layouts.app')

@section('title', 'Data Mahasiswa')

@include('layouts.partials.datatables-assets')

@section('content')
    <x-page-header judul="Data Mahasiswa"
        deskripsi="Pencarian dan pengurutan diproses di server.">
        @if (Auth::user()->role === 'admin')
            <a href="{{ route('mahasiswa.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Tambah Mahasiswa
            </a>
        @endif
    </x-page-header>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tabel-mahasiswa"
                    class="table table-striped table-hover align-middle w-100">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>NIM</th>
                            <th>Nama</th>
                            <th>JK</th>
                            <th>Email</th>
                            <th>Angkatan</th>
                            <th>Program Studi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function () {
            $('#tabel-mahasiswa').DataTable({
                processing: true,
                serverSide: true,
                ajax: @json(route('mahasiswa.data', [], false)),
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex',
                        orderable: false, searchable: false },
                    { data: 'nim', name: 'nim' },
                    { data: 'nama', name: 'nama' },
                    { data: 'jenis_kelamin', name: 'jenis_kelamin',
                        className: 'text-nowrap',
                        render: function (d) {
                            return d === 'L' ? 'Laki-laki' : 'Perempuan';
                        } },
                    { data: 'email', name: 'email' },
                    { data: 'angkatan', name: 'angkatan' },
                    { data: 'program_studi', name: 'program_studi' },
                    { data: 'aksi', name: 'aksi', className: 'text-nowrap',
                        orderable: false, searchable: false },
                ],
                order: [[2, 'asc']],
                language: {
                    processing: 'Memuat data...',
                    search: 'Cari:',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                    infoEmpty: 'Tidak ada data',
                    infoFiltered: '(disaring dari _MAX_ data)',
                    zeroRecords: 'Data tidak ditemukan',
                    paginate: {
                        first: 'Awal', last: 'Akhir',
                        next: 'Berikutnya', previous: 'Sebelumnya',
                    },
                },
            });
        });
    </script>
@endpush
