@foreach ([
    'warning' => 'warning',
    'info' => 'info',
] as $kunci => $kelas)
    @if (session($kunci))
        <div class="alert alert-{{ $kelas }} alert-dismissible fade show"
            role="alert">
            {{ session($kunci) }}
            <button type="button" class="btn-close"
                data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif
@endforeach

@if (session('success') || session('error'))
    @push('scripts')
        <script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}">
        </script>
        <script>
            const toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });

            @if (session('success'))
                toast.fire({
                    icon: 'success',
                    title: @json(session('success')),
                });
            @endif

            @if (session('error'))
                toast.fire({
                    icon: 'error',
                    title: @json(session('error')),
                });
            @endif
        </script>
    @endpush
@endif
