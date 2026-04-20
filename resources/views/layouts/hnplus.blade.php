<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('/images/favicon.ico') }}" type="image/x-icon">

    <title>N-PluS</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"
        integrity="sha384-tViUnnbplgXzC6JwBRKMEbLbmXPRfuAdDoTlMnLO9vUpqZeO3KkAuQzjvKe3nByh" crossorigin="anonymous">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer">

    <!-- App CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <!-- Beautiful Admin theme -->
    <link href="{{ asset('css/beautiful_admin.css') }}" rel="stylesheet">

    <!-- Bootstrap Datepicker CSS -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"
        integrity="sha512-mSYUmp1HYZDFaVKK//63EcZq4iFWFjxSL+Z3T/iJ9+ekCK6q1CUnFab8B0FvMvFdcJ8qakqWu0GkEMGPDUNZA=="
        crossorigin="anonymous" referrerpolicy="no-referrer">

    <style>
        .datepicker-thai {
            cursor: pointer !important;
            background-color: #fff !important;
            transition: all 0.2s ease;
            font-size: 0.9rem !important;
            position: relative !important;
            z-index: 5 !important;
            pointer-events: auto !important;
        }

        .datepicker-thai:focus {
            box-shadow: none !important;
            border-color: #23a7a7 !important;
        }

        /* HARDENED DATEPICKER UI */
        .datepicker {
            z-index: 2100 !important;
            padding: 10px !important;
            border-radius: 12px !important;
            border: none !important;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
            font-family: 'Prompt', sans-serif !important;
        }

        .datepicker table {
            width: 100% !important;
            border-collapse: separate !important;
            border-spacing: 2px !important;
        }

        .datepicker table tr td, 
        .datepicker table tr th {
            width: 38px !important;
            height: 38px !important;
            text-align: center !important;
            vertical-align: middle !important;
            border-radius: 8px !important;
            border: none !important;
            font-size: 0.85rem !important;
        }

        .datepicker table tr td.day:hover {
            background: #ebf5ff !important;
            color: #23a7a7 !important;
        }

        .datepicker table tr td.active, 
        .datepicker table tr td.active:hover {
            background: var(--primary-gradient) !important;
            color: white !important;
            font-weight: bold !important;
        }

        .datepicker .datepicker-switch {
            font-weight: bold !important;
            color: #1e293b !important;
            font-size: 0.95rem !important;
        }

        .datepicker .prev, .datepicker .next,
        .datepicker .datepicker-switch,
        .datepicker .today {
            cursor: pointer !important;
            color: #23a7a7 !important;
        }

        .datepicker .prev:hover, .datepicker .next:hover,
        .datepicker .today:hover {
            background: #ebf5ff !important;
            color: #1a7e7e !important;
        }

        .datepicker .today {
            font-weight: 700 !important;
            text-align: center !important;
            padding: 8px 0 !important;
            color: var(--primary-color) !important;
        }

        .datepicker .dow {
            color: #64748b !important;
            font-weight: 600 !important;
            font-size: 0.75rem !important;
            text-transform: uppercase;
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
                                    @if (isset($app_settings['icu_active']) && $app_settings['icu_active'] == 'Y')
                                        <li>
                                            <a class="dropdown-item py-2" href="{{ url('product/icu_report') }}">
                                                <i class="fa-solid fa-heart-pulse text-danger me-2 ms-1"></i>งานผู้ป่วย ICU
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
                                        <li>
                                            <a class="dropdown-item py-2" href="{{ url('product/opd_holiday_report') }}">
                                                <i class="bi bi-calendar-heart text-warning me-2"></i>งานผู้ป่วยนอก OPD วันหยุด
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
                                <i class="bi bi-code-slash me-1"></i>V. 69-04-20 16:00
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
                            <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- jQuery (Needed for Datepicker) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <!-- Bootstrap Datepicker JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.th.min.js"></script>

    <script>
        jQuery(document).ready(function ($) {
            // Function to initialize datepicker with consistent settings
            function initDatePicker(element) {
                var $el = $(element);
                // Already initialized check (but we'll re-init if requested)
                if ($el.data('datepicker-initialized')) return;

                $el.datepicker({
                    format: {
                        toDisplay: function (date, format, language) {
                            if (!date) return "";
                            var d = new Date(date);
                            if (isNaN(d.getTime())) return "";
                            var day = d.getDate();
                            var month = d.getMonth();
                            var year = d.getFullYear() + 543;
                            var thaiMonths = ["ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค."];
                            return day + ' ' + thaiMonths[month] + ' ' + year;
                        },
                        toValue: function (date, format, language) {
                            if (!date) return new Date();
                            if (date instanceof Date) return date;
                            
                            var dateStr = String(date).trim().replace(/\s+/g, ' ');
                            var parts = dateStr.split(' ');
                            
                            if (parts.length === 3) {
                                var day = parseInt(parts[0]);
                                var thaiMonths = ["ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค."];
                                var month = thaiMonths.indexOf(parts[1]);
                                if (month === -1) {
                                    var thaiMonthsFull = ["มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"];
                                    month = thaiMonthsFull.indexOf(parts[1]);
                                }
                                var year = parseInt(parts[2]);
                                // Logic: If it looks like Thai BE (e.g. 2569), convert to Gregorian (2026)
                                if (year > 2400) {
                                    year -= 543;
                                } else if (year < 100) {
                                    // Handle cases like '69' -> 2026
                                    year += (year > 40 ? 1900 : 2000);
                                }
                                
                                var d = new Date(year, month !== -1 ? month : 0, day);
                                return isNaN(d.getTime()) ? new Date() : d;
                            }
                            return new Date(date); // Fallback to native parsing
                        }
                    },
                    language: 'th',
                    autoclose: true,
                    todayHighlight: true,
                    todayBtn: 'linked',
                    orientation: 'bottom auto',
                    container: 'body',
                    templates: {
                        leftArrow: '«',
                        rightArrow: '»'
                    }
                }).on('show changeMonth changeYear changeDecade changeCentury', function () {
                    var patchHeader = function() {
                        $('.datepicker-switch').each(function () {
                            var text = $(this).text();
                            var match = text.match(/(\d{4})/);
                            if (match) {
                                var year = parseInt(match[1]);
                                if (year < 2400) {
                                    $(this).text(text.replace(match[1], year + 543));
                                }
                            }
                        });
                    };
                    setTimeout(patchHeader, 1);
                    setTimeout(patchHeader, 50);
                    setTimeout(patchHeader, 200);
                });
                
                $el.data('datepicker-initialized', true);
            }

            // Initialize all current and future datepickers
            if (typeof $.fn.datepicker !== 'undefined') {
                // Aggressive initialization on multiple events
                $(document).on('focus mousedown touchstart', '.datepicker-thai', function() {
                    initDatePicker(this);
                });

                // Initial pass
                $('.datepicker-thai').each(function() {
                    initDatePicker(this);
                });

                // Icon click trigger - explicitly show it
                $(document).on('click', '.input-group-text', function (e) {
                    var input = $(this).closest('.input-group').find('.datepicker-thai');
                    if (input.length) {
                        initDatePicker(input[0]);
                        input.datepicker('show');
                        input.focus();
                    }
                });
            } else {
                console.error("Bootstrap Datepicker library NOT found on jQuery object.");
            }
        });
    </script>

    <!-- ✅ เปิดช่องให้แต่ละ page ใส่ JS ของตัวเอง -->
    @stack('scripts')

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