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
            padding: 3rem 2.5rem;
            position: relative;
            box-shadow: 0 10px 30px rgba(13, 110, 253, 0.15);
            overflow: hidden;
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
            /* Non-clickable cursor */
            cursor: default;
        }

        /* Removed hover effects */
        /* .stat-card:hover { transform: translateY(-8px); ... } */

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
        <!-- Welcome Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div
                    class="dashboard-header d-flex flex-wrap align-items-center justify-content-between position-relative overflow-hidden">
                    <div class="z-1">
                        <h2 class="fw-bold mb-2">ยินดีต้อนรับสู่ Nurse Plus 👋</h2>
                        <p class="mb-0 opacity-75">เข้าสู่ระบบผลิตภาพทางการพยาบาล</p>
                        <p class="mt-3 mb-0 small"><i class="bi bi-clock me-2"></i><span
                                id="live-clock">{{ \Carbon\Carbon::now()->locale('th')->isoFormat('LL LTS') }}</span></p>

                        @auth
                            <a href="{{ route('hnplus.dashboard') }}"
                                class="btn btn-light text-primary fw-bold mt-3 shadow-sm rounded-pill px-4">
                                <i class="bi bi-speedometer2 me-2"></i>ไปยังหน้า Dashboard
                            </a>
                        @else
                            <button type="button" class="btn btn-light text-primary fw-bold mt-3 shadow-sm rounded-pill px-4"
                                data-bs-toggle="modal" data-bs-target="#loginModal">
                                <i class="bi bi-box-arrow-in-right me-2"></i>เข้าสู่ระบบ
                            </button>
                        @endauth
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

        <!-- Main Dashboard Grid (Non-clickable) -->
        <div class="row g-4">
            <!-- ER Report -->
            <div class="col-12 col-xl-6">
                <div class="stat-card h-100 position-relative overflow-hidden">
                    <div class="card-status-bar bg-danger"></div>
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon-circle bg-danger bg-opacity-10 text-danger">
                                <i class="bi bi-heart-pulse-fill"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-1">งานอุบัติเหตุ-ฉุกเฉิน</h5>
                                <div class="d-flex align-items-center text-muted small">
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
                                <div class="display-6 fw-bold text-danger mb-1">{{ $er_stats['resuscitation'] }}</div>
                                <div class="small fw-semibold text-danger text-opacity-75 text-truncate">Resuscitation</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-item p-3 rounded-4 text-center h-100 border border-warning border-opacity-25"
                                style="background-color: rgba(253, 126, 20, 0.1);">
                                <div class="display-6 fw-bold mb-1" style="color: #fd7e14;">{{ $er_stats['emergent'] }}
                                </div>
                                <div class="small fw-semibold text-truncate" style="color: #fd7e14;">Emergent</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div
                                class="stat-item p-3 rounded-4 bg-soft-warning text-center h-100 border border-warning border-opacity-25">
                                <div class="display-6 fw-bold text-warning mb-1">{{ $er_stats['urgent'] }}</div>
                                <div class="small fw-semibold text-warning text-opacity-75 text-truncate">Urgent</div>
                            </div>
                        </div>

                        <!-- Row 2: Bottom 2 Severities -->
                        <div class="col-4">
                            <div
                                class="stat-item p-3 rounded-4 bg-soft-success text-center h-100 border border-success border-opacity-10">
                                <div class="display-6 fw-bold text-success mb-1">{{ $er_stats['semi_urgent'] }}</div>
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
            </div>

            <!-- IPD Report -->
            <div class="col-12 col-xl-6">
                <div class="stat-card h-100 position-relative overflow-hidden">
                    <div class="card-status-bar bg-primary"></div>
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon-circle bg-primary bg-opacity-10 text-primary">
                                <i class="fa-solid fa-bed-pulse"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-1">ผู้ป่วยใน</h5>
                                <div class="d-flex align-items-center text-muted small">
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
                                <div class="small fw-bold text-danger text-opacity-75" style="font-size: 0.7rem;">Critical
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div
                                class="stat-item p-2 py-3 rounded-4 bg-soft-warning text-center h-100 border border-warning border-opacity-25">
                                <div class="h3 fw-bold text-warning mb-1">{{ $ipd_stats['semi_critical'] }}</div>
                                <div class="small fw-bold text-warning text-opacity-75" style="font-size: 0.7rem;">Semi-Cri
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div
                                class="stat-item p-2 py-3 rounded-4 bg-soft-info text-center h-100 border border-info border-opacity-25">
                                <div class="h3 fw-bold text-info mb-1">{{ $ipd_stats['moderate'] }}</div>
                                <div class="small fw-bold text-info text-opacity-75" style="font-size: 0.7rem;">Moderate
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div
                                class="stat-item p-2 py-3 rounded-4 bg-soft-success text-center h-100 border border-success border-opacity-10">
                                <div class="h3 fw-bold text-success mb-1">{{ $ipd_stats['convalescent'] }}</div>
                                <div class="small fw-bold text-success text-opacity-75" style="font-size: 0.7rem;">Conv.
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-item p-2 py-3 rounded-4 bg-light text-center h-100 border text-muted">
                                <div class="h3 fw-bold text-dark mb-1">{{ $ipd_stats['severe_type_null'] }}</div>
                                <div class="small fw-bold text-muted" style="font-size: 0.65rem;">ไม่บันทึกความรุนแรง
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- OPD Report -->
            <div class="col-12 col-md-6 col-xl-4">
                <div class="stat-card h-100 position-relative overflow-hidden">
                    <div class="card-status-bar bg-success"></div>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon-circle bg-success bg-opacity-10 text-success">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-1">ผู้ป่วยนอก</h5>
                                <div class="d-flex align-items-center text-muted small">
                                    <i class="bi bi-clock-history me-1"></i> {{ $opd_stats['shift'] }}
                                </div>
                            </div>
                        </div>
                        <span class="badge bg-success rounded-pill px-3 py-2">OPD</span>
                    </div>

                    <div class="d-flex align-items-center justify-content-between bg-light rounded-4 p-4 border">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center"
                                style="width: 48px; height: 48px;">
                                <i class="bi bi-people fs-4"></i>
                            </div>
                            <span class="text-muted fw-medium">ผู้ป่วยทั้งหมดในเวร</span>
                        </div>
                        <div class="display-4 fw-bold text-success">{{ $opd_stats['patient_all'] }}</div>
                    </div>
                </div>
            </div>

            <!-- NCD Report -->
            <div class="col-12 col-md-6 col-xl-4">
                <div class="stat-card h-100 position-relative overflow-hidden">
                    <div class="card-status-bar bg-warning"></div>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon-circle bg-warning bg-opacity-10 text-warning">
                                <i class="bi bi-heart-pulse-fill"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-1">คลินิก NCD</h5>
                                <div class="d-flex align-items-center text-muted small">
                                    <i class="bi bi-clock-history me-1"></i> {{ $ncd_stats['shift'] }}
                                </div>
                            </div>
                        </div>
                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2">NCD</span>
                    </div>

                    <div class="d-flex align-items-center justify-content-between bg-light rounded-4 p-4 border">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center"
                                style="width: 48px; height: 48px;">
                                <i class="bi bi-clipboard2-pulse fs-4"></i>
                            </div>
                            <span class="text-muted fw-medium">ผู้ป่วยทั้งหมดในเวร</span>
                        </div>
                        <div class="display-4 fw-bold text-warning text-dark">{{ $ncd_stats['patient_all'] }}</div>
                    </div>
                </div>
            </div>

            <!-- ARI Report -->
            <div class="col-12 col-md-6 col-xl-4">
                <div class="stat-card h-100 position-relative overflow-hidden">
                    <div class="card-status-bar bg-secondary"></div>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon-circle bg-secondary bg-opacity-10 text-secondary">
                                <i class="bi bi-mask"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-1">คลินิก ARI</h5>
                                <div class="d-flex align-items-center text-muted small">
                                    <i class="bi bi-clock-history me-1"></i> {{ $ari_stats['shift'] }}
                                </div>
                            </div>
                        </div>
                        <span class="badge bg-secondary rounded-pill px-3 py-2">ARI</span>
                    </div>

                    <div class="d-flex align-items-center justify-content-between bg-light rounded-4 p-4 border">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center"
                                style="width: 48px; height: 48px;">
                                <i class="bi bi-person-bounding-box fs-4"></i>
                            </div>
                            <span class="text-muted fw-medium">ผู้ป่วยทั้งหมดในเวร</span>
                        </div>
                        <div class="display-4 fw-bold text-secondary">{{ $ari_stats['patient_all'] }}</div>
                    </div>
                </div>
            </div>
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
