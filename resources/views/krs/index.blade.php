@extends('layouts.app')

@section('title', 'Kartu Rencana Studi')

@include('layouts.partials.datatables-assets')

@section('content')
    <x-page-header judul="Kartu Rencana Studi"
        deskripsi="Kelola KRS mahasiswa beserta mata kuliahnya.">
        @if (Auth::user()->role === 'admin')
            <a href="{{ route('krs.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Buat KRS
            </a>
        @endif
    </x-page-header>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body d-flex flex-wrap gap-3 align-items-end">
            <div>
                <label for="filter-tahun" class="form-label small mb-1">
                    Tahun Akademik
                </label>
                <select id="filter-tahun" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    @foreach ($daftarTahun as $tahun)
                        <option value="{{ $tahun }}">{{ $tahun }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="filter-semester" class="form-label small mb-1">
                    Semester
                </label>
                <select id="filter-semester" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    @foreach ($daftarSemester as $semester)
                        <option value="{{ $semester }}">{{ $semester }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="button" id="reset-filter"
                    class="btn btn-sm btn-outline-secondary">Reset</button>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tabel-krs"
                    class="table table-striped table-hover align-middle w-100">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>NIM</th>
                            <th>Nama Mahasiswa</th>
                            <th>Tahun</th>
                            <th>Semester</th>
                            <th>Total SKS</th>
                            <th>Status</th>
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
            const tabel = $('#tabel-krs').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: @json(route('krs.data', [], false)),
                    data: function (d) {
                        d.tahun_akademik = $('#filter-tahun').val();
                        d.semester = $('#filter-semester').val();
                    },
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex',
                        orderable: false, searchable: false },
                    { data: 'nim_mahasiswa', name: 'nim_mahasiswa' },
                    { data: 'nama_mahasiswa', name: 'nama_mahasiswa' },
                    { data: 'tahun_akademik', name: 'tahun_akademik' },
                    { data: 'semester', name: 'semester' },
                    { data: 'total_sks', name: 'total_sks',
                        orderable: false, searchable: false },
                    { data: 'status', name: 'status', render: function (d) {
                        const warna = {
                            draft: 'secondary', diajukan: 'info',
                            disetujui: 'success', ditolak: 'danger',
                        }[d] || 'secondary';
                        return '<span class="badge text-bg-' + warna + '">'
                            + d.charAt(0).toUpperCase() + d.slice(1) + '</span>';
                    } },
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

            $('#filter-tahun, #filter-semester').on('change', function () {
                tabel.ajax.reload();
            });

            $('#reset-filter').on('click', function () {
                $('#filter-tahun').val('');
                $('#filter-semester').val('');
                tabel.ajax.reload();
            });
        });
    </script>
@endpush
