@extends('layouts.master-without-nav')
@section('title')
    @lang('translation.signin')
@endsection
@section('content')

    <div class="auth-page-wrapper auth-bg-cover py-5 d-flex justify-content-center align-items-center min-vh-100">
        <div class="bg-overlay"></div>
        <div class="auth-page-content overflow-hidden pt-lg-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card overflow-hidden">
                            <div class="row g-0">
                                <div class="col-lg-6">
                                    <div class="p-lg-5 p-4 auth-one-bg h-100">
                                        <div class="bg-overlay"></div>
                                        <div class="position-relative h-100 d-flex flex-column">
                                            <div class="mb-4 text-center">
                                                <a href="index" class="d-block">
                                                    <div class="text-center mb-4">
                                                        <img src="{{ asset('build/images/logo_elections_large.png') }}"
                                                            alt="Electoral Control"
                                                            class="img-fluid"
                                                            style="max-height: 80px;">
                                                    </div>
                                                    {{-- <div class="bolivian-symbol mx-auto" style="width: fit-content; background: rgba(255,255,255,0.2); padding: 10px; border-radius: 8px; color: white; font-weight: bold;">BO</div> --}}
                                                </a>
                                            </div>
                                            <div class="mt-auto text-white text-center">
                                                <h1 class="text-white">SISTEMA DE CÓMPUTO</h1>
                                                <p class="fs-15">
                                                    Sistema de gestión, monitoreo y consolidación de resultados electorales
                                                </p>
                                                <div id="qoutescarouselIndicators" class="carousel slide mt-4" data-bs-ride="carousel">
                                                    <div class="carousel-indicators">
                                                        <button type="button" data-bs-target="#qoutescarouselIndicators" data-bs-slide-to="0" class="active" aria-current="true"></button>
                                                        <button type="button" data-bs-target="#qoutescarouselIndicators" data-bs-slide-to="1"></button>
                                                    </div>
                                                    <div class="carousel-inner text-center text-white-50 pb-5">
                                                        <div class="carousel-item active">
                                                            <p class="fs-15 fst-italic">"Seguridad y transparencia en cada proceso electoral."</p>
                                                        </div>
                                                        <div class="carousel-item">
                                                            <p class="fs-15 fst-italic">"Consolidando los resultados con eficiencia y precisión."</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="p-lg-5 p-4">
                                        <div>
                                            <h5 class="text-primary">Bienvenido !</h5>
                                            <p class="text-muted">Ingresa tus credenciales para continuar.</p>
                                        </div>

                                        <div class="mt-4">
                                            <form action="{{ route('login') }}" method="POST">
                                                @csrf
                                                <div class="mb-3">
                                                    <label for="username" class="form-label">Usuario <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control @error('email') is-invalid @enderror"
                                                           value="{{ old('email') }}" id="username" name="email" placeholder="Ingresa tu usuario">
                                                    @error('email')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label" for="password-input">Contraseña <span class="text-danger">*</span></label>
                                                    <div class="position-relative auth-pass-inputgroup mb-3">
                                                        <input type="password" class="form-control pe-5 password-input @error('password') is-invalid @enderror"
                                                               name="password" placeholder="Ingresa tu contraseña" id="password-input">
                                                        <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon"
                                                                type="button" id="password-addon">
                                                            <i class="ri-eye-fill align-middle"></i>
                                                        </button>
                                                        @error('password')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="remember" id="auth-remember-check">
                                                    <label class="form-check-label" for="auth-remember-check">Recordarme</label>
                                                </div>

                                                <div class="mt-4">
                                                    <button class="btn btn-success w-100" type="submit">Ingresar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <footer class="footer start-0">
                <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <p class="mb-0 text-muted">&copy;
                                <script>document.write(new Date().getFullYear())</script> Sistema de Control Electoral <i class="mdi mdi-heart text-danger"></i>
                            </p>
                        </div>
                    </div>
                </div>
                </div>
            </footer>
    </div>
@endsection
@section('script')
    <script src="{{ URL::asset('build/js/pages/password-addon.init.js') }}"></script>
@endsection
