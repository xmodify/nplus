<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('/images/favicon.ico') }}" type="image/x-icon">

    <title>N-PluS</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- App CSS (ถ้ามี) -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <style>
        body { background-color:#f8f9fa; }
        .navbar-hnplus { background-color:#23A7A7; }
        .text-hnplus { color: #03a9f4 !important; }
    </style>
    
</head>

<body>
<div id="app">

<!-- ================= NAVBAR ================= -->
<nav class="navbar navbar-expand-lg navbar-dark navbar-hnplus shadow-sm">
    <div class="container-fluid">

        <a class="navbar-brand btn btn-outline-info text-white fw-bold"
           href="{{ url('/') }}">
            <i class="bi bi-hospital"></i> N-PluS
        </a>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMain">

            <!-- LEFT (ตามที่กำหนดมาเท่านั้น) -->
            <ul class="navbar-nav me-auto">
                @auth
                <li class="nav-item dropdown">
                    <a class="btn btn-outline-info dropdown-toggle text-white"
                       href="#"
                       data-bs-toggle="dropdown"
                       aria-expanded="false">
                        ผลิตภาพทางการพยาบาล
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end bg-info">
                        <li><a class="dropdown-item link-primary text-white" href="{{ url('hnplus/product/er_report') }}">งานอุบัติเหตุ-ฉุกเฉิน ER</a></li>
                        <li><a class="dropdown-item link-primary text-white" href="{{ url('hnplus/product/ipd_report') }}">งานผู้ป่วยในสามัญ IPD</a></li>
                        <li><a class="dropdown-item link-primary text-white" href="{{ url('hnplus/product/opd_report') }}">งานผู้ป่วยนอก OPD</a></li>
                        <li><a class="dropdown-item link-primary text-white" href="{{ url('hnplus/product/ncd_report') }}">งานผู้ป่วย NCD</a></li>
                    </ul>
                </li>
                @endauth
            </ul>

            <!-- RIGHT -->
            <ul class="navbar-nav ms-auto align-items-center">
                 <li > 
                    <div class="btn text-white">
                        V. 69-01-22 15:00
                    </div>   
                </li> 

                @guest
                <li class="nav-item">
                    <button class="btn btn-outline-info text-white"
                            data-bs-toggle="modal"
                            data-bs-target="#loginModal">
                        <i class="bi bi-box-arrow-in-right"></i> Login
                    </button>
                </li>
                @else
                <li class="nav-item dropdown">
                    <a class="nav-link btn btn-outline-info dropdown-toggle text-white"
                       href="#"
                       data-bs-toggle="dropdown">
                        {{ Auth::user()->name }}
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end bg-info">
                        <li>
                            <a class="dropdown-item link-primary text-white" 
                                href="{{ route('admin.main_setting') }}">
                                Main Setting
                            </a>
                            <a class="dropdown-item link-primary text-white"
                               href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                               document.getElementById('logout-form').submit();">
                                Logout
                            </a>
                        </li>
                    </ul>
                </li>

                <form id="logout-form"
                      action="{{ route('logout') }}"
                      method="POST"
                      class="d-none">
                    @csrf
                </form>
                @endguest

            </ul>

        </div>
    </div>
</nav>

<!-- ================= CONTENT ================= -->
<main class="container-fluid py-4">
    @yield('content')
</main>

</div>

<!-- ================= LOGIN MODAL ================= -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <input type="hidden" name="redirect" value="{{ url()->current() }}">

                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-lock"></i> เข้าสู่ระบบ
                    </h5>
                    <button type="button"
                            class="btn-close btn-close-white"
                            data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    @if ($errors->any())
                        <div class="alert alert-danger small">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email"
                               name="email"
                               value="{{ old('email') }}"
                               class="form-control"
                               required>
                    </div>

                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password"
                               name="password"
                               class="form-control"
                               required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        ปิด
                    </button>
                    <button type="submit"
                            class="btn btn-info text-white">
                        Login
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- ================= JS ================= -->

<!-- Bootstrap Bundle (สำคัญมาก) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

@if ($errors->any())
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof bootstrap !== 'undefined') {
        const modalEl = document.getElementById('loginModal');
        if (modalEl) {
            new bootstrap.Modal(modalEl).show();
        }
    }
});
</script>
@endif
</body>
</html>
