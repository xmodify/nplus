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

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- App CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <!-- Beautiful Admin theme -->
    <link href="{{ asset('css/beautiful_admin.css') }}" rel="stylesheet">

    <!-- Bootstrap Datepicker CSS -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">

    <style>
        .datepicker-thai {
            cursor: pointer !important;
            background-color: #fff !important;
            transition: all 0.2s ease;
            font-size: 0.875rem !important;
        }

        .datepicker-thai:focus {
            z-index: 0 !important;
            box-shadow: none !important;
        }

        .input-group-date-custom {
            background-color: #fff;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
            width: 160px;
        }

        .input-group-date-custom:hover,
        .input-group-date-custom:focus-within {
            border-color: #23a7a7;
            box-shadow: 0 0 0 3px rgba(35, 167, 167, 0.1);
        }

        .input-group-date-custom .input-group-text {
            border: none;
            background: transparent;
            padding-right: 0.5rem;
            color: #23a7a7;
        }

        .input-group-date-custom .form-control {
            border: none;
            background: transparent;
            padding-left: 0.5rem;
        }

        .navbar-hnplus {
            background: rgba(35, 167, 167, 0.95) !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .navbar-brand {
            font-size: 1.5rem;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }

        .navbar-brand:hover {
            transform: scale(1.05);
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
        }

        .nav-link-premium {
            font-weight: 600;
            margin: 0 5px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .nav-link-premium:hover {
            background: rgba(255, 255, 255, 0.15);
        }
    </style>

</head>

<body>
    <div id="app">

        <!-- ================= NAVBAR ================= -->
        <nav class="navbar navbar-expand-lg navbar-dark navbar-hnplus sticky-top shadow-sm">
            <div class="container-fluid px-md-4">

                <a class="navbar-brand fw-bold" href="{{ url('/') }}">
                    <i class="bi bi-hospital-fill me-2"></i>N-PluS
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarMain">

                    <!-- LEFT (ตามที่กำหนดมาเท่านั้น) -->
                    <ul class="navbar-nav me-auto">
                        @auth
                            @php $app_settings = \App\Models\MainSetting::pluck('value', 'name')->toArray(); @endphp
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle text-white nav-link-premium" href="#"
                                    data-bs-toggle="dropdown">
                                    <i class="bi bi-file-earmark-bar-graph me-1"></i>ผลิตภาพทางการพยาบาล
                                </a>

                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0">
                                    @if (isset($app_settings['er_active']) && $app_settings['er_active'] == 'Y')
                                        <li>
                                            <a class="dropdown-item py-2" href="{{ url('product/er_report') }}">
                                                <i class="bi bi-hospital text-danger me-2"></i>งานอุบัติเหตุ-ฉุกเฉิน ER
                                            </a>
                                        </li>
                                    @endif
                                    @if (isset($app_settings['ipd_active']) && $app_settings['ipd_active'] == 'Y')
                                        <li>
                                            <a class="dropdown-item py-2" href="{{ url('product/ipd_report') }}">
                                                <i class="fa-solid fa-bed text-success me-2 ms-1"></i>งานผู้ป่วยในสามัญ IPD
                                            </a>
                                        </li>
                                    @endif
                                    @if (isset($app_settings['vip_active']) && $app_settings['vip_active'] == 'Y')
                                        <li>
                                            <a class="dropdown-item py-2" href="{{ url('product/vip_report') }}">
                                                <i class="fa-solid fa-couch text-warning me-2 ms-1"></i>งานผู้ป่วยห้องพิเศษ
                                                VIP
                                            </a>
                                        </li>
                                    @endif
                                    @if (isset($app_settings['lr_active']) && $app_settings['lr_active'] == 'Y')
                                        <li>
                                            <a class="dropdown-item py-2" href="{{ url('product/lr_report') }}">
                                                <i
                                                    class="fa-solid fa-person-breastfeeding text-danger me-2 ms-1"></i>งานห้องคลอด
                                                LR
                                            </a>
                                        </li>
                                    @endif
                                    @if (isset($app_settings['opd_active']) && $app_settings['opd_active'] == 'Y')
                                        <li>
                                            <a class="dropdown-item py-2" href="{{ url('product/opd_report') }}">
                                                <i class="bi bi-person-lines-fill text-primary me-2"></i>งานผู้ป่วยนอก OPD
                                            </a>
                                        </li>
                                    @endif
                                    @if (isset($app_settings['ncd_active']) && $app_settings['ncd_active'] == 'Y')
                                        <li>
                                            <a class="dropdown-item py-2" href="{{ url('product/ncd_report') }}">
                                                <i class="bi bi-heart-pulse text-info me-2"></i>งานผู้ป่วย NCD
                                            </a>
                                        </li>
                                    @endif
                                    @if (isset($app_settings['ari_active']) && $app_settings['ari_active'] == 'Y')
                                        <li>
                                            <a class="dropdown-item py-2" href="{{ url('product/ari_report') }}">
                                                <i class="bi bi-thermometer-half text-warning me-2"></i>งานผู้ป่วย ARI
                                            </a>
                                        </li>
                                    @endif
                                    @if (isset($app_settings['ckd_active']) && $app_settings['ckd_active'] == 'Y')
                                        <li>
                                            <a class="dropdown-item py-2" href="{{ url('product/ckd_report') }}">
                                                <i class="bi bi-heart-pulse text-dark me-2"></i>งานผู้ป่วย CKD
                                            </a>
                                        </li>
                                    @endif
                                    @if (isset($app_settings['hd_active']) && $app_settings['hd_active'] == 'Y')
                                        <li>
                                            <a class="dropdown-item py-2" href="{{ url('product/hd_report') }}">
                                                <i class="bi bi-droplet-fill text-primary me-2"></i>งานฟอกเลือดไตเทียม HD
                                            </a>
                                        </li>
                                    @endif
                                    @if (isset($app_settings['anc_active']) && $app_settings['anc_active'] == 'Y')
                                        <li>
                                            <a class="dropdown-item py-2" href="{{ url('product/anc_report') }}">
                                                <i
                                                    class="fa-solid fa-person-breastfeeding text-success me-2 ms-1"></i>งานฝากครรภ์
                                                ANC
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endauth
                    </ul>

                    <!-- RIGHT -->
                    <ul class="navbar-nav ms-auto align-items-center">
                        <li>
                            <div class="badge bg-white text-primary rounded-pill px-3 py-2 shadow-sm fw-bold">
                                <i class="bi bi-code-slash me-1"></i>V. 69-02-26 11:00
                            </div>
                        </li>

                        @guest
                            <li class="nav-item">
                                <button class="btn btn-outline-info text-white" data-bs-toggle="modal"
                                    data-bs-target="#loginModal">
                                    <i class="bi bi-box-arrow-in-right"></i> Login
                                </button>
                            </li>
                        @else
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle text-white nav-link-premium" href="#"
                                    data-bs-toggle="dropdown">
                                    <i class="bi bi-person-circle me-1"></i>{{ Auth::user()->name }}
                                </a>

                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0">
                                    @if (Auth::user()->role == 'admin')
                                        <li>
                                            <a class="dropdown-item py-2" href="{{ route('admin.main_setting') }}">
                                                <i class="bi bi-gear me-2"></i>ตั้งค่า MainSetting
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-2" href="{{ route('admin.users.index') }}">
                                                <i class="bi bi-people me-2"></i>ตั้งค่าผู้ใช้งาน
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-2" href="{{ route('admin.budget_year.index') }}">
                                                <i class="bi bi-calendar-event me-2"></i>ตั้งค่าปีงบประมาณ
                                            </a>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                    @endif
                                    <li>
                                        <a class="dropdown-item text-danger py-2 fw-bold" href="{{ route('logout') }}"
                                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                        </li>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
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

                    <div class="modal-header text-white" style="background: var(--primary-gradient);">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-shield-lock-fill me-2"></i>เข้าสู่ระบบ
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        @if ($errors->any())
                            <div class="alert alert-danger small">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="form-control"
                                required>
                        </div>

                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                    </div>

                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">
                            ปิด
                        </button>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">
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

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- jQuery (Needed for Datepicker) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap Datepicker JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.th.min.js">
    </script>

    <script>
        $(document).ready(function() {
            if (typeof $.fn.datepicker !== 'undefined') {
                var dp = $('.datepicker-thai').datepicker({
                    format: {
                        toDisplay: function(date, format, language) {
                            var d = new Date(date);
                            var day = d.getDate();
                            var month = d.getMonth();
                            var year = d.getFullYear() + 543;
                            var thaiMonths = ["ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.",
                                "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค."
                            ];
                            return day + ' ' + thaiMonths[month] + ' ' + year;
                        },
                        toValue: function(date, format, language) {
                            var parts = date.split(' ');
                            if (parts.length === 3) {
                                var day = parseInt(parts[0]);
                                var thaiMonths = ["ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.",
                                    "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค."
                                ];
                                var month = thaiMonths.indexOf(parts[1]);
                                if (month === -1) {
                                    // Fallback full month
                                    var thaiMonthsFull = ["มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน",
                                        "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน",
                                        "ตุลาคม", "พฤศจิกายน", "ธันวาคม"
                                    ];
                                    month = thaiMonthsFull.indexOf(parts[1]);
                                }
                                var year = parseInt(parts[2]);
                                if (year > 2400) year -= 543;
                                return new Date(year, month !== -1 ? month : 0, day);
                            }
                            return new Date();
                        }
                    },
                    language: 'th',
                    autoclose: true,
                    todayHighlight: true,
                    todayBtn: 'linked',
                    orientation: 'bottom auto'
                });

                // Manual Thai BE Year patch for header
                dp.on('show', function() {
                    var $this = $(this);
                    setTimeout(function() {
                        $('.datepicker-days .datepicker-switch, .datepicker-months .datepicker-switch, .datepicker-years .datepicker-switch')
                            .each(function() {
                                var text = $(this).text();
                                var match = text.match(/\d{4}/);
                                if (match && parseInt(match[0]) < 2400) {
                                    $(this).text(text.replace(match[0], parseInt(match[0]) +
                                        543));
                                }
                            });
                    }, 5);
                });

                // Icon click trigger
                $('.input-group-text').on('click', function(e) {
                    var input = $(this).closest('.input-group').find('.datepicker-thai');
                    if (input.length) {
                        input.datepicker('show');
                    }
                });
            }
        });
    </script>

    <!-- ✅ เปิดช่องให้แต่ละ page ใส่ JS ของตัวเอง -->
    @stack('scripts')

    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
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
