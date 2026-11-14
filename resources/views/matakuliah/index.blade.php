@extends('layouts.app')

@section('title', 'Data Mata Kuliah')

@include('layouts.partials.datatables-assets')

@section('content')
    <x-page-header judul="Data Mata Kuliah"
        deskripsi="Pencarian dan pengurutan diproses di server.">
        @if (Auth::user()->role === 'admin')
            <a href="{{ route('matakuliah.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Tambah Mata Kuliah
            </a>
        @endif
    </x-page-header>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tabel-matakuliah"
                    class="table table-striped table-hover align-middle w-100">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Kode</th>
                            <th>Nama Mata Kuliah</th>
                            <th>SKS</th>
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
            $('#tabel-matakuliah').DataTable({
                processing: true,
                serverSide: true,
                ajax: @json(route('matakuliah.data', [], false)),
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex',
                        orderable: false, searchable: false },
                    { data: 'kode_mk', name: 'kode_mk' },
                    { data: 'nama_mk', name: 'nama_mk' },
                    { data: 'sks', name: 'sks' },
                    { data: 'aksi', name: 'aksi', className: 'text-nowrap',
                        orderable: false, searchable: false },
                ],
                order: [[1, 'asc']],
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
