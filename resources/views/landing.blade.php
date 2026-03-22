@extends('layouts.master-without-nav')
@section('title', 'Centro de Monitoreo')

@section('content')
    <div class="layout-wrapper">
        <nav class="navbar navbar-expand-lg bg-dark" id="navbar">
            <div class="container">
                <a class="navbar-brand" href="index">
                    <img src="{{ URL::asset('build/images/logo_elections_large.png') }}" class="card-logo card-logo-dark" alt="logo dark" height="50">
                    <img src="{{ URL::asset('build/images/logo_elections_large.png') }}" class="card-logo card-logo-light" alt="logo light" height="50">
                </a>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mx-auto mt-2 mt-lg-0" id="navbar-example"></ul>
                    <div class="d-flex align-items-center">
                        <div class="ms-1 header-item d-none d-sm-flex">
                            <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle"
                                    data-toggle="fullscreen" id="fullscreen-btn">
                                <i class='bx bx-fullscreen fs-22'></i>
                            </button>
                        </div>

                        <div class="ms-1 header-item d-none d-sm-flex">
                            <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle light-dark-mode">
                                <i class='bx bx-moon fs-22'></i>
                            </button>
                        </div>
                        <div class="ms-2">
                            @auth
                                <a href="/" class="btn btn-primary">Admin Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-primary">Ingresar</a>
                            @endauth
                        </div>
                    </div>
                </div>
                <button class="navbar-toggler py-0 fs-20 text-body" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <i class="mdi mdi-menu text-white"></i>
                </button>
            </div>
        </nav>
        <div class="vertical-overlay" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent.show"></div>
        <div class="row justify-content-center mt-2">
            <div class="col-lg-11">
            <div class="card p-2 pb-0 mb-0">
                <div class='card-header'>
                    <div>
                        <h5 class="card-title mb-0">Panel de Resultados Electorales</h5>
                        <p class="text-muted mb-0">Última actualización: {{ now()->format('d/m/Y H:i') }}</p>
                    </div>
                    @auth
                    @if(auth()->user()->hasPermission('manage_settings'))
                    <div class="dashboard-controls">
                        <button id="toggleDashboardBtn"
                                class="btn {{ $dashboard->is_public ? 'btn-warning' : 'btn-success' }}"
                                data-current-status="{{ $dashboard->is_public ? 'true' : 'false' }}"
                                data-toggle-url="{{ route('dashboard.toggle') }}">
                            <i class="{{ $dashboard->is_public ? 'ri-lock-unlock-fill' : 'ri-rotate-lock-fill' }}"
                            id="toggleIcon"></i>
                            <span id="toggleText">
                                {{ $dashboard->is_public ? 'Deshabilitar Dashboard' : 'Habilitar Dashboard' }}
                            </span>
                        </button>
                    </div>
                    @endif
                    @endauth
                </div>
            </div>
            @include('partials.dashboard-content')
            </div>
        </div>
        <footer class="custom-footer bg-dark py-3 border-0 mt-0" 
        style="border-top: none !important; box-shadow: none !important; background-color: #212529 !important; position: relative; z-index: 10;">
            <div class="container">
                <div class="row text-center text-sm-start align-items-center">
                    <div class="col-sm-6 text-white-50">
                        © {{ date('Y') }} Sistema de Procesamiento Electoral
                    </div>
                    <div class="col-sm-6 text-sm-end text-white-50">
                        Plataforma de análisis Quillacollo 2026
                    </div>
                </div>
            </div>
        </footer>
        {{-- <button onclick="topFunction()" class="btn btn-danger btn-icon landing-back-top" id="back-to-top">
            <i class="ri-arrow-up-line"></i>
        </button> --}}
    </div>
@endsection
@section('script')
    @yield('dashboard-scripts')
    <script src="{{ URL::asset('build/libs/swiper/swiper-bundle.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modeBtn = document.querySelector('.light-dark-mode');
            if (modeBtn) {
                modeBtn.addEventListener('click', function() {
                    let currentTheme = document.documentElement.getAttribute('data-bs-theme') || 'dark';
                    let newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                    document.documentElement.setAttribute('data-bs-theme', newTheme);
                    document.documentElement.setAttribute('data-theme', newTheme);
                    document.documentElement.setAttribute('data-layout-mode', newTheme);
                    sessionStorage.setItem('data-bs-theme', newTheme);
                    sessionStorage.setItem('data-theme', newTheme);
                });
            }
            const fsBtn = document.getElementById('fullscreen-btn');
            if (fsBtn) {
                fsBtn.addEventListener('click', function() {
                    if (!document.fullscreenElement) {
                        document.documentElement.requestFullscreen();
                    } else {
                        document.exitFullscreen();
                    }
                });
            }
        });
    </script>
    @auth
    @if(auth()->user()->hasPermission('manage_settings'))
    <script>
    document.getElementById('toggleDashboardBtn')?.addEventListener('click', function () {
        const btn          = this;
        const url          = btn.dataset.toggleUrl;
        const originalHtml = btn.innerHTML;
        btn.disabled  = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Procesando...';
        fetch(url, {
            method:  'POST',
            headers: {
                'Content-Type':     'application/json',
                'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
        .then(r => {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
        })
        .then(data => {
            if (!data.success) throw new Error(data.message ?? 'Error desconocido');
            const nowPublic = data.is_public;
            btn.dataset.currentStatus = nowPublic ? 'true' : 'false';
            btn.className  = 'btn ' + (nowPublic ? 'btn-warning' : 'btn-success');
            btn.innerHTML  = `<i class="${nowPublic ? 'ri-lock-unlock-fill' : 'ri-rotate-lock-fill'}" id="toggleIcon"></i> `
                           + `<span id="toggleText">${nowPublic ? 'Deshabilitar Dashboard' : 'Habilitar Dashboard'}</span>`;
        })
        .catch(err => {
            btn.innerHTML = originalHtml;
            alert('Error: ' + err.message);
        })
        .finally(() => { btn.disabled = false; });
    });
    </script>
    @endif
    @endauth
@endsection
