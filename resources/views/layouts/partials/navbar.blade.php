<nav class="navbar navbar-dark bg-success sticky-top shadow-sm">
    <div class="container-fluid">
        <div class="d-flex align-items-center">
            <button class="navbar-toggler d-lg-none me-2" type="button"
                data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu"
                aria-controls="sidebarMenu" aria-label="Buka menu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand fw-semibold" href="{{ route('dashboard') }}">
                <i class="bi bi-mortarboard-fill me-1"></i>
                {{ config('app.name') }}
            </a>
        </div>

        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-outline-light" id="tombolTema"
                aria-label="Ganti ke tema gelap">
                <i class="bi bi-moon-stars-fill" id="ikonTema"></i>
            </button>

            <div class="dropdown">
                <button class="btn btn-success dropdown-toggle" type="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle"></i>
                    <span class="d-none d-sm-inline">Administrator</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><span class="dropdown-item-text small">Akun contoh</span></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item disabled" href="#">Keluar</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

@push('scripts')
    <script>
        (() => {
            const html = document.documentElement;
            const tombol = document.getElementById('tombolTema');
            const ikon = document.getElementById('ikonTema');

            const terapkan = (tema) => {
                html.setAttribute('data-bs-theme', tema);
                localStorage.setItem('tema', tema);

                ikon.className = tema === 'dark'
                    ? 'bi bi-sun-fill'
                    : 'bi bi-moon-stars-fill';
                tombol.setAttribute('aria-label', tema === 'dark'
                    ? 'Ganti ke tema terang'
                    : 'Ganti ke tema gelap');
            };

            const tersimpan = localStorage.getItem('tema');
            const temaAwal = tersimpan ?? (window.matchMedia('(prefers-color-scheme: dark)').matches
                ? 'dark'
                : 'light');
            terapkan(temaAwal);

            tombol.addEventListener('click', () => {
                terapkan(html.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark');
            });
        })();
    </script>
@endpush
