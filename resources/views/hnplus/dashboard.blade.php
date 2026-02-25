@extends('layouts.hnplus')

@section('content')
    <style>
        :root {
            --primary-bg: #f3f6f9;
            --card-border-radius: 20px;
        }

        body {
            background-color: var(--primary-bg);
            font-family: 'Prompt', sans-serif;
        }

        .dashboard-header {
            background: linear-gradient(120deg, #20c997, #0d6efd);
            border-radius: var(--card-border-radius);
            color: white;
            padding: 2rem 1.5rem;
            /* Reduced padding for mobile */
            position: relative;
            box-shadow: 0 10px 30px rgba(13, 110, 253, 0.15);
            overflow: hidden;
        }

        @media (min-width: 768px) {
            .dashboard-header {
                padding: 3rem 2.5rem;
            }
        }

        .dashboard-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 70%);
            border-radius: 50%;
        }

        .stat-card {
            background: white;
            border-radius: var(--card-border-radius);
            padding: 1.75rem;
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            border: 1px solid rgba(0, 0, 0, 0.04);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
            border-color: rgba(13, 110, 253, 0.2);
        }

        .icon-circle {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            transition: transform 0.3s ease;
        }

        .stat-card:hover .icon-circle {
            transform: scale(1.1) rotate(5deg);
        }

        .card-status-bar {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
        }

        /* Override Bootstrap bg-soft classes for custom modern feel */
        .bg-soft-primary {
            background-color: #ebf5ff !important;
            color: #0065ff !important;
        }

        .bg-soft-success {
            background-color: #e6fffa !important;
            color: #00a082 !important;
        }

        .bg-soft-warning {
            background-color: #fffbeb !important;
            color: #b45309 !important;
        }

        .bg-soft-danger {
            background-color: #fef2f2 !important;
            color: #ef4444 !important;
        }

        .bg-soft-info {
            background-color: #f0f9ff !important;
            color: #0284c7 !important;
        }
    </style>

    <div class="container py-3">
        @php $app_settings = \App\Models\MainSetting::pluck('value', 'name')->toArray(); @endphp
        <!-- Welcome Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div
                    class="dashboard-header d-flex flex-wrap align-items-center justify-content-between position-relative overflow-hidden">
                    <div class="z-1">
                        <h2 class="fw-bold mb-2">ยินดีต้อนรับ, {{ Auth::user()->name ?? 'Guest' }} 👋</h2>
                        <p class="mb-0 opacity-75">เข้าสู่ระบบผลิตภาพทางการพยาบาล (Nurse Plus)</p>
                        <p class="mt-3 mb-0 small">
                            <i class="bi bi-clock me-2"></i><span id="live-clock"
                                class="fw-bold">{{ \Carbon\Carbon::now()->locale('th')->isoFormat('LL LTS') }}</span>
                        </p>
                    </div>
                    <div class="d-none d-md-block z-1">
                        <img src="{{ asset('images/logo_hnplus1.png') }}" alt="HN-Plus Logo" height="100"
                            class="bg-white rounded-3 p-2 shadow-sm">
                    </div>
                    <!-- Decorative Circle -->
                    <div class="position-absolute end-0 bottom-0 opacity-10" style="transform: translate(30%, 30%);">
                        <i class="bi bi-heart-pulse-fill" style="font-size: 15rem; color: white;"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Dashboard Grid -->
        <div class="row g-4">
            @if (isset($app_settings['er_active']) && $app_settings['er_active'] == 'Y')
                <!-- ER Report -->
                <div class="col-12 col-xl-6">
                    <a href="{{ url('product/er_report') }}" class="text-decoration-none">
                        <div class="stat-card h-100 position-relative overflow-hidden">
                            <div class="card-status-bar bg-danger"></div>
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="icon-circle bg-danger bg-opacity-10 text-danger">
                                        <i class="bi bi-heart-pulse-fill"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold text-dark mb-1">งานอุบัติเหตุ-ฉุกเฉิน</h5>
                                        <div class="d-flex align-items-center text-muted small flex-wrap">
                                            <i class="bi bi-clock-history me-1"></i> {{ $er_stats['shift'] }}
                                        </div>
                                    </div>
                                </div>
                                <span class="badge bg-danger rounded-pill px-3 py-2">ER</span>
                            </div>

                            <div class="row g-3">
                                <!-- Row 1: Top 3 Severities -->
                                <div class="col-4">
                                    <div
                                        class="stat-item p-3 rounded-4 bg-soft-danger text-center h-100 border border-danger border-opacity-10">
                                        <div class="display-6 fw-bold text-danger mb-1">{{ $er_stats['resuscitation'] }}
                                        </div>
                                        <div class="small fw-semibold text-danger text-opacity-75 text-truncate">
                                            Resuscitation
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-item p-3 rounded-4 text-center h-100 border border-warning border-opacity-25"
                                        style="background-color: rgba(253, 126, 20, 0.1);">
                                        <div class="display-6 fw-bold mb-1" style="color: #fd7e14;">
                                            {{ $er_stats['emergent'] }}
                                        </div>
                                        <div class="small fw-semibold text-truncate" style="color: #fd7e14;">Emergent</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div
                                        class="stat-item p-3 rounded-4 bg-soft-warning text-center h-100 border border-warning border-opacity-25">
                                        <div class="display-6 fw-bold text-warning mb-1">{{ $er_stats['urgent'] }}</div>
                                        <div class="small fw-semibold text-warning text-opacity-75 text-truncate">Urgent
                                        </div>
                                    </div>
                                </div>

                                <!-- Row 2: Bottom 2 Severities -->
                                <div class="col-4">
                                    <div
                                        class="stat-item p-3 rounded-4 bg-soft-success text-center h-100 border border-success border-opacity-10">
                                        <div class="display-6 fw-bold text-success mb-1">{{ $er_stats['semi_urgent'] }}
                                        </div>
                                        <div class="small fw-semibold text-success text-opacity-75">Semi Urgent</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-item p-3 rounded-4 bg-light text-center h-100 border text-muted">
                                        <div class="display-6 fw-bold text-dark mb-1">{{ $er_stats['non_urgent'] }}</div>
                                        <div class="small fw-semibold">Non Urgent</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-item p-3 rounded-4 bg-light text-center h-100 border text-muted">
                                        <div class="display-6 fw-bold text-dark mb-1">{{ $er_stats['unknown'] }}</div>
                                        <div class="small fw-semibold text-truncate">ไม่บันทึกความรุนแรง</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endif

            @if (isset($app_settings['ipd_active']) && $app_settings['ipd_active'] == 'Y')
                <!-- IPD Report -->
                <div class="col-12 col-xl-6">
                    <a href="{{ url('product/ipd_report') }}" class="text-decoration-none">
                        <div class="stat-card h-100 position-relative overflow-hidden">
                            <div class="card-status-bar bg-primary"></div>
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="icon-circle bg-primary bg-opacity-10 text-primary">
                                        <i class="fa-solid fa-bed-pulse"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold text-dark mb-1">ผู้ป่วยใน</h5>
                                        <div class="d-flex align-items-center text-muted small flex-wrap">
                                            <i class="bi bi-clock-history me-1"></i> {{ $ipd_stats['shift'] }}
                                        </div>
                                    </div>
                                </div>
                                <span class="badge bg-primary rounded-pill px-3 py-2">IPD</span>
                            </div>

                            <div class="row g-3">
                                <div class="col-4">
                                    <div
                                        class="stat-item p-2 py-3 rounded-4 bg-soft-danger text-center h-100 border border-danger border-opacity-10">
                                        <div class="h3 fw-bold text-danger mb-1">{{ $ipd_stats['critical'] }}</div>
                                        <div class="small fw-bold text-danger text-opacity-75" style="font-size: 0.7rem;">
                                            Critical</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div
                                        class="stat-item p-2 py-3 rounded-4 bg-soft-warning text-center h-100 border border-warning border-opacity-25">
                                        <div class="h3 fw-bold text-warning mb-1">{{ $ipd_stats['semi_critical'] }}</div>
                                        <div class="small fw-bold text-warning text-opacity-75" style="font-size: 0.7rem;">
                                            Semi-Cri</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div
                                        class="stat-item p-2 py-3 rounded-4 bg-soft-info text-center h-100 border border-info border-opacity-25">
                                        <div class="h3 fw-bold text-info mb-1">{{ $ipd_stats['moderate'] }}</div>
                                        <div class="small fw-bold text-info text-opacity-75" style="font-size: 0.7rem;">
                                            Moderate
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div
                                        class="stat-item p-2 py-3 rounded-4 bg-soft-success text-center h-100 border border-success border-opacity-10">
                                        <div class="h3 fw-bold text-success mb-1">{{ $ipd_stats['convalescent'] }}</div>
                                        <div class="small fw-bold text-success text-opacity-75" style="font-size: 0.7rem;">
                                            Conv.
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-item p-2 py-3 rounded-4 bg-light text-center h-100 border text-muted">
                                        <div class="h3 fw-bold text-dark mb-1">{{ $ipd_stats['severe_type_null'] }}</div>
                                        <div class="small fw-bold text-muted" style="font-size: 0.65rem;">
                                            ไม่บันทึกความรุนแรง
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endif

            @if (isset($app_settings['vip_active']) && $app_settings['vip_active'] == 'Y')
                <!-- VIP Report -->
                <div class="col-12 col-xl-6">
                    <a href="{{ url('product/vip_report') }}" class="text-decoration-none">
                        <div class="stat-card h-100 position-relative overflow-hidden">
                            <div class="card-status-bar bg-warning"></div>
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="icon-circle bg-warning bg-opacity-10 text-warning">
                                        <i class="fa-solid fa-couch"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold text-dark mb-1">งานผู้ป่วยห้องพิเศษ VIP</h5>
                                        <div class="d-flex align-items-center text-muted small flex-wrap">
                                            <i class="bi bi-clock-history me-1"></i> {{ $vip_stats['shift'] }}
                                        </div>
                                    </div>
                                </div>
                                <span class="badge bg-warning text-dark rounded-pill px-3 py-2">VIP</span>
                            </div>

                            <div class="row g-3">
                                <div class="col-4">
                                    <div
                                        class="stat-item p-2 py-3 rounded-4 bg-soft-danger text-center h-100 border border-danger border-opacity-10">
                                        <div class="h3 fw-bold text-danger mb-1">{{ $vip_stats['critical'] }}</div>
                                        <div class="small fw-bold text-danger text-opacity-75" style="font-size: 0.7rem;">
                                            Critical</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div
                                        class="stat-item p-2 py-3 rounded-4 bg-soft-warning text-center h-100 border border-warning border-opacity-25">
                                        <div class="h3 fw-bold text-warning mb-1">{{ $vip_stats['semi_critical'] }}</div>
                                        <div class="small fw-bold text-warning text-opacity-75"
                                            style="font-size: 0.7rem;">
                                            Semi-Cri</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div
                                        class="stat-item p-2 py-3 rounded-4 bg-soft-info text-center h-100 border border-info border-opacity-25">
                                        <div class="h3 fw-bold text-info mb-1">{{ $vip_stats['moderate'] }}</div>
                                        <div class="small fw-bold text-info text-opacity-75" style="font-size: 0.7rem;">
                                            Moderate
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div
                                        class="stat-item p-2 py-3 rounded-4 bg-soft-success text-center h-100 border border-success border-opacity-10">
                                        <div class="h3 fw-bold text-success mb-1">{{ $vip_stats['convalescent'] }}</div>
                                        <div class="small fw-bold text-success text-opacity-75"
                                            style="font-size: 0.7rem;">
                                            Conv.
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-item p-2 py-3 rounded-4 bg-light text-center h-100 border text-muted">
                                        <div class="h3 fw-bold text-dark mb-1">{{ $vip_stats['severe_type_null'] }}</div>
                                        <div class="small fw-bold text-muted" style="font-size: 0.65rem;">
                                            ไม่บันทึกความรุนแรง
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endif

            @if (isset($app_settings['lr_active']) && $app_settings['lr_active'] == 'Y')
                <!-- LR Report -->
                <div class="col-12 col-xl-6">
                    <a href="{{ url('product/lr_report') }}" class="text-decoration-none">
                        <div class="stat-card h-100 position-relative overflow-hidden">
                            <div class="card-status-bar bg-danger"></div>
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="icon-circle bg-danger bg-opacity-10 text-danger">
                                        <i class="fa-solid fa-person-breastfeeding"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold text-dark mb-1">งานห้องคลอด LR</h5>
                                        <div class="d-flex align-items-center text-muted small flex-wrap">
                                            <i class="bi bi-clock-history me-1"></i> {{ $lr_stats['shift'] }}
                                        </div>
                                    </div>
                                </div>
                                <span class="badge bg-danger rounded-pill px-3 py-2">LR</span>
                            </div>

                            <div class="row g-3">
                                <div class="col-4">
                                    <div
                                        class="stat-item p-2 py-3 rounded-4 bg-soft-danger text-center h-100 border border-danger border-opacity-10">
                                        <div class="h3 fw-bold text-danger mb-1">{{ $lr_stats['critical'] }}</div>
                                        <div class="small fw-bold text-danger text-opacity-75" style="font-size: 0.7rem;">
                                            Critical</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div
                                        class="stat-item p-2 py-3 rounded-4 bg-soft-warning text-center h-100 border border-warning border-opacity-25">
                                        <div class="h3 fw-bold text-warning mb-1">{{ $lr_stats['semi_critical'] }}</div>
                                        <div class="small fw-bold text-warning text-opacity-75"
                                            style="font-size: 0.7rem;">
                                            Semi-Cri</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div
                                        class="stat-item p-2 py-3 rounded-4 bg-soft-info text-center h-100 border border-info border-opacity-25">
                                        <div class="h3 fw-bold text-info mb-1">{{ $lr_stats['moderate'] }}</div>
                                        <div class="small fw-bold text-info text-opacity-75" style="font-size: 0.7rem;">
                                            Moderate
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div
                                        class="stat-item p-2 py-3 rounded-4 bg-soft-success text-center h-100 border border-success border-opacity-10">
                                        <div class="h3 fw-bold text-success mb-1">{{ $lr_stats['convalescent'] }}</div>
                                        <div class="small fw-bold text-success text-opacity-75"
                                            style="font-size: 0.7rem;">
                                            Conv.
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-item p-2 py-3 rounded-4 bg-light text-center h-100 border text-muted">
                                        <div class="h3 fw-bold text-dark mb-1">{{ $lr_stats['severe_type_null'] }}</div>
                                        <div class="small fw-bold text-muted" style="font-size: 0.65rem;">
                                            ไม่บันทึกความรุนแรง
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endif

            @if (isset($app_settings['opd_active']) && $app_settings['opd_active'] == 'Y')
                <!-- OPD Report -->
                <div class="col-12 col-md-6 col-xl-4">
                    <a href="{{ url('product/opd_report') }}" class="text-decoration-none">
                        <div class="stat-card h-100 position-relative overflow-hidden">
                            <div class="card-status-bar bg-success"></div>
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="icon-circle bg-success bg-opacity-10 text-success">
                                        <i class="bi bi-people-fill"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold text-dark mb-1">ผู้ป่วยนอก</h5>
                                        <div class="d-flex align-items-center text-muted small flex-wrap">
                                            <i class="bi bi-clock-history me-1"></i> {{ $opd_stats['shift'] }}
                                        </div>
                                    </div>
                                </div>
                                <span class="badge bg-success rounded-pill px-3 py-2">OPD</span>
                            </div>

                            <div
                                class="d-flex align-items-center justify-content-between bg-light rounded-4 p-3 p-sm-4 border">
                                <div class="d-flex align-items-center gap-2 gap-sm-3">
                                    <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center flex-shrink-0"
                                        style="width: 40px; height: 40px; @media (min-width: 576px) { width: 48px; height: 48px; }">
                                        <i class="bi bi-people fs-5 fs-sm-4"></i>
                                    </div>
                                    <span class="text-muted fw-medium small">ผู้ป่วยทั้งหมดในเวร</span>
                                </div>
                                <div class="display-6 display-sm-4 fw-bold text-success">{{ $opd_stats['patient_all'] }}
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endif

            @if (isset($app_settings['ncd_active']) && $app_settings['ncd_active'] == 'Y')
                <!-- NCD Report -->
                <div class="col-12 col-md-6 col-xl-4">
                    <a href="{{ url('product/ncd_report') }}" class="text-decoration-none">
                        <div class="stat-card h-100 position-relative overflow-hidden">
                            <div class="card-status-bar bg-warning"></div>
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="icon-circle bg-warning bg-opacity-10 text-warning">
                                        <i class="bi bi-heart-pulse-fill"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold text-dark mb-1">คลินิก NCD</h5>
                                        <div class="d-flex align-items-center text-muted small flex-wrap">
                                            <i class="bi bi-clock-history me-1"></i> {{ $ncd_stats['shift'] }}
                                        </div>
                                    </div>
                                </div>
                                <span class="badge bg-warning text-dark rounded-pill px-3 py-2">NCD</span>
                            </div>

                            <div
                                class="d-flex align-items-center justify-content-between bg-light rounded-4 p-3 p-sm-4 border">
                                <div class="d-flex align-items-center gap-2 gap-sm-3">
                                    <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center flex-shrink-0"
                                        style="width: 40px; height: 40px; @media (min-width: 576px) { width: 48px; height: 48px; }">
                                        <i class="bi bi-clipboard2-pulse fs-5 fs-sm-4"></i>
                                    </div>
                                    <span class="text-muted fw-medium small">ผู้ป่วยทั้งหมดในเวร</span>
                                </div>
                                <div class="display-6 display-sm-4 fw-bold text-warning text-dark">
                                    {{ $ncd_stats['patient_all'] }}</div>
                            </div>
                        </div>
                    </a>
                </div>
            @endif

            @if (isset($app_settings['ari_active']) && $app_settings['ari_active'] == 'Y')
                <!-- ARI Report -->
                <div class="col-12 col-md-6 col-xl-4">
                    <a href="{{ url('product/ari_report') }}" class="text-decoration-none">
                        <div class="stat-card h-100 position-relative overflow-hidden">
                            <div class="card-status-bar bg-secondary"></div>
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="icon-circle bg-secondary bg-opacity-10 text-secondary">
                                        <i class="bi bi-mask"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold text-dark mb-1">คลินิก ARI</h5>
                                        <div class="d-flex align-items-center text-muted small flex-wrap">
                                            <i class="bi bi-clock-history me-1"></i> {{ $ari_stats['shift'] }}
                                        </div>
                                    </div>
                                </div>
                                <span class="badge bg-secondary rounded-pill px-3 py-2">ARI</span>
                            </div>

                            <div
                                class="d-flex align-items-center justify-content-between bg-light rounded-4 p-3 p-sm-4 border">
                                <div class="d-flex align-items-center gap-2 gap-sm-3">
                                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center flex-shrink-0"
                                        style="width: 40px; height: 40px; @media (min-width: 576px) { width: 48px; height: 48px; }">
                                        <i class="bi bi-person-bounding-box fs-5 fs-sm-4"></i>
                                    </div>
                                    <span class="text-muted fw-medium small">ผู้ป่วยทั้งหมดในเวร</span>
                                </div>
                                <div class="display-6 display-sm-4 fw-bold text-secondary">{{ $ari_stats['patient_all'] }}
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endif

            @if (isset($app_settings['ckd_active']) && $app_settings['ckd_active'] == 'Y')
                <!-- CKD Report -->
                <div class="col-12 col-md-6 col-xl-4">
                    <a href="{{ url('product/ckd_report') }}" class="text-decoration-none">
                        <div class="stat-card h-100 position-relative overflow-hidden">
                            <div class="card-status-bar bg-info"></div>
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="icon-circle bg-info bg-opacity-10 text-info">
                                        <i class="fa-solid fa-water"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold text-dark mb-1">คลินิก CKD</h5>
                                        <div class="d-flex align-items-center text-muted small flex-wrap">
                                            <i class="bi bi-clock-history me-1"></i> {{ $ckd_stats['shift'] }}
                                        </div>
                                    </div>
                                </div>
                                <span class="badge bg-info rounded-pill px-3 py-2">CKD</span>
                            </div>

                            <div
                                class="d-flex align-items-center justify-content-between bg-light rounded-4 p-3 p-sm-4 border">
                                <div class="d-flex align-items-center gap-2 gap-sm-3">
                                    <div class="rounded-circle bg-info text-white d-flex align-items-center justify-content-center flex-shrink-0"
                                        style="width: 40px; height: 40px;">
                                        <i class="bi bi-people fs-5"></i>
                                    </div>
                                    <span class="text-muted fw-medium small">ผู้ป่วยทั้งหมดในเวร</span>
                                </div>
                                <div class="display-6 fw-bold text-info">{{ $ckd_stats['patient_all'] }}</div>
                            </div>
                        </div>
                    </a>
                </div>
            @endif

            @if (isset($app_settings['hd_active']) && $app_settings['hd_active'] == 'Y')
                <!-- HD Report -->
                <div class="col-12 col-md-6 col-xl-4">
                    <a href="{{ url('product/hd_report') }}" class="text-decoration-none">
                        <div class="stat-card h-100 position-relative overflow-hidden">
                            <div class="card-status-bar bg-secondary"></div>
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="icon-circle bg-secondary bg-opacity-10 text-secondary">
                                        <i class="fa-solid fa-hospital-user"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold text-dark mb-1">หน่วยไตเทียม HD</h5>
                                        <div class="d-flex align-items-center text-muted small flex-wrap">
                                            <i class="bi bi-clock-history me-1"></i> {{ $hd_stats['shift'] }}
                                        </div>
                                    </div>
                                </div>
                                <span class="badge bg-secondary rounded-pill px-3 py-2">HD</span>
                            </div>

                            <div
                                class="d-flex align-items-center justify-content-between bg-light rounded-4 p-3 p-sm-4 border">
                                <div class="d-flex align-items-center gap-2 gap-sm-3">
                                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center flex-shrink-0"
                                        style="width: 40px; height: 40px;">
                                        <i class="bi bi-people fs-5"></i>
                                    </div>
                                    <span class="text-muted fw-medium small">ผู้ป่วยทั้งหมดในเวร</span>
                                </div>
                                <div class="display-6 fw-bold text-secondary">{{ $hd_stats['patient_all'] }}</div>
                            </div>
                        </div>
                    </a>
                </div>
            @endif
        </div>


    </div>

    @push('scripts')
        <script>
            function updateClock() {
                const now = new Date();
                const options = {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    weekday: 'long',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false
                };
                document.getElementById('live-clock').textContent = now.toLocaleDateString('th-TH', options);
            }
            // Update immediately and then every second
            updateClock();
            setInterval(updateClock, 1000);

            // Reload page every 1 minute (60000 ms)
            setInterval(function() {
                window.location.reload();
            }, 60000);
        </script>
    @endpush
@endsection
