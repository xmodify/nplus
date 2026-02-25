@extends('layouts.hnplus')

@section('content')
    <div class="container mt-4 mb-0 fade-in-up">

        <!-- ================= ACTION BUTTONS ================= -->
        <div class="mb-4 d-flex gap-2">
            <button class="btn btn-danger px-4 shadow-sm" id="gitPullBtn">
                <i class="bi bi-git me-2"></i>Git Pull
            </button>

            <form id="structureForm" method="POST" action="{{ route('admin.up_structure') }}" style="display:inline;">
                @csrf
                <button type="button" class="btn btn-primary px-4 shadow-sm text-white"
                    style="background-color: #0d6efd !important; border-color: #0d6efd !important;"
                    onclick="confirmUpgrade()">
                    <i class="bi bi-arrow-repeat me-2"></i>Upgrade Structure
                </button>
            </form>
        </div>

        <!-- ================= OUTPUT ================= -->
        <pre id="gitOutput" style="background:#eeee; padding:1rem; border-radius:6px; margin-bottom: 20px;"></pre>

        <!-- ================= SETTINGS TABS ================= -->
        <div class="card border-0 mb-4 h-auto shadow-sm">
            <div class="card-header">
                <strong><i class="bi bi-gear-fill me-2"></i>ตั้งค่าระบบ (Main Setting)</strong>
            </div>
            <div class="card-body p-4">

                <form action="{{ route('admin.main_setting.update') }}" method="POST">
                    @csrf

                    <ul class="nav nav-tabs" id="settingTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general"
                                type="button" role="tab" aria-controls="general" aria-selected="true">General</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="er-tab" data-bs-toggle="tab" data-bs-target="#er" type="button"
                                role="tab" aria-controls="er" aria-selected="false">ER</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="ipd-tab" data-bs-toggle="tab" data-bs-target="#ipd"
                                type="button" role="tab" aria-controls="ipd" aria-selected="false">IPD</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="vip-tab" data-bs-toggle="tab" data-bs-target="#vip"
                                type="button" role="tab" aria-controls="vip" aria-selected="false">VIP</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="lr-tab" data-bs-toggle="tab" data-bs-target="#lr" type="button"
                                role="tab" aria-controls="lr" aria-selected="false">LR</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="opd-tab" data-bs-toggle="tab" data-bs-target="#opd"
                                type="button" role="tab" aria-controls="opd" aria-selected="false">OPD</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="ncd-tab" data-bs-toggle="tab" data-bs-target="#ncd"
                                type="button" role="tab" aria-controls="ncd" aria-selected="false">NCD</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="ari-tab" data-bs-toggle="tab" data-bs-target="#ari"
                                type="button" role="tab" aria-controls="ari" aria-selected="false">ARI</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="ckd-tab" data-bs-toggle="tab" data-bs-target="#ckd"
                                type="button" role="tab" aria-controls="ckd" aria-selected="false">CKD</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="hd-tab" data-bs-toggle="tab" data-bs-target="#hd"
                                type="button" role="tab" aria-controls="hd" aria-selected="false">HD</button>
                        </li>
                    </ul>

                    <div class="tab-content pt-3" id="settingTabsContent">
                        <!-- General Tab -->
                        <div class="tab-pane fade show active" id="general" role="tabpanel"
                            aria-labelledby="general-tab">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 50%">Name (TH)</th>
                                            <th style="width: 50%">Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($general_settings as $setting)
                                            <tr>
                                                <td>{{ $setting->name_th }}</td>
                                                <td>
                                                    @if (str_ends_with($setting->name, '_active'))
                                                        <select class="form-select border-primary"
                                                            name="{{ $setting->name }}">
                                                            <option value="Y"
                                                                {{ $setting->value == 'Y' ? 'selected' : '' }}>เปิดใช้งาน
                                                                (Y)</option>
                                                            <option value="N"
                                                                {{ $setting->value == 'N' ? 'selected' : '' }}>ปิดใช้งาน (N)
                                                            </option>
                                                        </select>
                                                    @else
                                                        <input type="text" class="form-control"
                                                            name="{{ $setting->name }}" value="{{ $setting->value }}">
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- ER Tab -->
                        <div class="tab-pane fade" id="er" role="tabpanel" aria-labelledby="er-tab">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 50%">Name (TH)</th>
                                            <th style="width: 50%">Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($er_settings as $setting)
                                            <tr>
                                                <td>{{ $setting->name_th }}</td>
                                                <td>
                                                    @if (str_ends_with($setting->name, '_active'))
                                                        <select class="form-select border-primary"
                                                            name="{{ $setting->name }}">
                                                            <option value="Y"
                                                                {{ $setting->value == 'Y' ? 'selected' : '' }}>เปิดใช้งาน
                                                                (Y)</option>
                                                            <option value="N"
                                                                {{ $setting->value == 'N' ? 'selected' : '' }}>ปิดใช้งาน
                                                                (N)</option>
                                                        </select>
                                                    @else
                                                        <input type="text" class="form-control"
                                                            name="{{ $setting->name }}" value="{{ $setting->value }}">
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- IPD Tab -->
                        <div class="tab-pane fade" id="ipd" role="tabpanel" aria-labelledby="ipd-tab">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 50%">Name (TH)</th>
                                            <th style="width: 50%">Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($ipd_settings as $setting)
                                            <tr>
                                                <td>{{ $setting->name_th }}</td>
                                                <td>
                                                    @if (str_ends_with($setting->name, '_active'))
                                                        <select class="form-select border-primary"
                                                            name="{{ $setting->name }}">
                                                            <option value="Y"
                                                                {{ $setting->value == 'Y' ? 'selected' : '' }}>เปิดใช้งาน
                                                                (Y)</option>
                                                            <option value="N"
                                                                {{ $setting->value == 'N' ? 'selected' : '' }}>ปิดใช้งาน
                                                                (N)</option>
                                                        </select>
                                                    @else
                                                        <input type="text" class="form-control"
                                                            name="{{ $setting->name }}" value="{{ $setting->value }}">
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- OPD Tab -->
                        <div class="tab-pane fade" id="opd" role="tabpanel" aria-labelledby="opd-tab">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 50%">Name (TH)</th>
                                            <th style="width: 50%">Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($opd_settings as $setting)
                                            <tr>
                                                <td>{{ $setting->name_th }}</td>
                                                <td>
                                                    @if (str_ends_with($setting->name, '_active'))
                                                        <select class="form-select border-primary"
                                                            name="{{ $setting->name }}">
                                                            <option value="Y"
                                                                {{ $setting->value == 'Y' ? 'selected' : '' }}>เปิดใช้งาน
                                                                (Y)</option>
                                                            <option value="N"
                                                                {{ $setting->value == 'N' ? 'selected' : '' }}>ปิดใช้งาน
                                                                (N)</option>
                                                        </select>
                                                    @else
                                                        <input type="text" class="form-control"
                                                            name="{{ $setting->name }}" value="{{ $setting->value }}">
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- NCD Tab -->
                        <div class="tab-pane fade" id="ncd" role="tabpanel" aria-labelledby="ncd-tab">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 50%">Name (TH)</th>
                                            <th style="width: 50%">Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($ncd_settings as $setting)
                                            <tr>
                                                <td>{{ $setting->name_th }}</td>
                                                <td>
                                                    @if (str_ends_with($setting->name, '_active'))
                                                        <select class="form-select border-primary"
                                                            name="{{ $setting->name }}">
                                                            <option value="Y"
                                                                {{ $setting->value == 'Y' ? 'selected' : '' }}>เปิดใช้งาน
                                                                (Y)</option>
                                                            <option value="N"
                                                                {{ $setting->value == 'N' ? 'selected' : '' }}>ปิดใช้งาน
                                                                (N)</option>
                                                        </select>
                                                    @else
                                                        <input type="text" class="form-control"
                                                            name="{{ $setting->name }}" value="{{ $setting->value }}">
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- ARI Tab -->
                        <div class="tab-pane fade" id="ari" role="tabpanel" aria-labelledby="ari-tab">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 50%">Name (TH)</th>
                                            <th style="width: 50%">Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($ari_settings as $setting)
                                            <tr>
                                                <td>{{ $setting->name_th }}</td>
                                                <td>
                                                    @if (str_ends_with($setting->name, '_active'))
                                                        <select class="form-select border-primary"
                                                            name="{{ $setting->name }}">
                                                            <option value="Y"
                                                                {{ $setting->value == 'Y' ? 'selected' : '' }}>เปิดใช้งาน
                                                                (Y)</option>
                                                            <option value="N"
                                                                {{ $setting->value == 'N' ? 'selected' : '' }}>ปิดใช้งาน
                                                                (N)</option>
                                                        </select>
                                                    @else
                                                        <input type="text" class="form-control"
                                                            name="{{ $setting->name }}" value="{{ $setting->value }}">
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- CKD Tab -->
                        <div class="tab-pane fade" id="ckd" role="tabpanel" aria-labelledby="ckd-tab">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 50%">Name (TH)</th>
                                            <th style="width: 50%">Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($ckd_settings as $setting)
                                            <tr>
                                                <td>{{ $setting->name_th }}</td>
                                                <td>
                                                    @if (str_ends_with($setting->name, '_active'))
                                                        <select class="form-select border-primary"
                                                            name="{{ $setting->name }}">
                                                            <option value="Y"
                                                                {{ $setting->value == 'Y' ? 'selected' : '' }}>เปิดใช้งาน
                                                                (Y)</option>
                                                            <option value="N"
                                                                {{ $setting->value == 'N' ? 'selected' : '' }}>ปิดใช้งาน
                                                                (N)</option>
                                                        </select>
                                                    @else
                                                        <input type="text" class="form-control"
                                                            name="{{ $setting->name }}" value="{{ $setting->value }}">
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- HD Tab -->
                        <div class="tab-pane fade" id="hd" role="tabpanel" aria-labelledby="hd-tab">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 50%">Name (TH)</th>
                                            <th style="width: 50%">Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($hd_settings as $setting)
                                            <tr>
                                                <td>{{ $setting->name_th }}</td>
                                                <td>
                                                    @if (str_ends_with($setting->name, '_active'))
                                                        <select class="form-select border-primary"
                                                            name="{{ $setting->name }}">
                                                            <option value="Y"
                                                                {{ $setting->value == 'Y' ? 'selected' : '' }}>เปิดใช้งาน
                                                                (Y)</option>
                                                            <option value="N"
                                                                {{ $setting->value == 'N' ? 'selected' : '' }}>ปิดใช้งาน
                                                                (N)</option>
                                                        </select>
                                                    @else
                                                        <input type="text" class="form-control"
                                                            name="{{ $setting->name }}" value="{{ $setting->value }}">
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- VIP Tab -->
                        <div class="tab-pane fade" id="vip" role="tabpanel" aria-labelledby="vip-tab">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 50%">Name (TH)</th>
                                            <th style="width: 50%">Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($vip_settings as $setting)
                                            <tr>
                                                <td>{{ $setting->name_th }}</td>
                                                <td>
                                                    @if (str_ends_with($setting->name, '_active'))
                                                        <select class="form-select border-primary"
                                                            name="{{ $setting->name }}">
                                                            <option value="Y"
                                                                {{ $setting->value == 'Y' ? 'selected' : '' }}>เปิดใช้งาน
                                                                (Y)</option>
                                                            <option value="N"
                                                                {{ $setting->value == 'N' ? 'selected' : '' }}>ปิดใช้งาน
                                                                (N)</option>
                                                        </select>
                                                    @else
                                                        <input type="text" class="form-control"
                                                            name="{{ $setting->name }}" value="{{ $setting->value }}">
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- LR Tab -->
                        <div class="tab-pane fade" id="lr" role="tabpanel" aria-labelledby="lr-tab">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 50%">Name (TH)</th>
                                            <th style="width: 50%">Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($lr_settings as $setting)
                                            <tr>
                                                <td>{{ $setting->name_th }}</td>
                                                <td>
                                                    @if (str_ends_with($setting->name, '_active'))
                                                        <select class="form-select border-primary"
                                                            name="{{ $setting->name }}">
                                                            <option value="Y"
                                                                {{ $setting->value == 'Y' ? 'selected' : '' }}>เปิดใช้งาน
                                                                (Y)</option>
                                                            <option value="N"
                                                                {{ $setting->value == 'N' ? 'selected' : '' }}>ปิดใช้งาน
                                                                (N)</option>
                                                        </select>
                                                    @else
                                                        <input type="text" class="form-control"
                                                            name="{{ $setting->name }}" value="{{ $setting->value }}">
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>



                    </div>

                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-success px-5 py-2 fw-bold shadow-sm">
                            <i class="bi bi-save me-2"></i>บันทึกการเปลี่ยนแปลง
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            function copyToClipboard(btn) {
                const input = btn.parentElement.querySelector('input');
                input.select();
                input.setSelectionRange(0, 99999);
                navigator.clipboard.writeText(input.value).then(() => {
                    const originalIcon = btn.innerHTML;
                    btn.innerHTML = '<i class="bi bi-check2 text-white"></i>';
                    btn.classList.add('btn-success');
                    btn.classList.remove('btn-outline-secondary');

                    setTimeout(() => {
                        btn.innerHTML = originalIcon;
                        btn.classList.remove('btn-success');
                        btn.classList.add('btn-outline-secondary');
                    }, 2000);
                });
            }
        </script>

        <!-- ================= HOSxP REFERENCE DATA ================= -->
        <div class="card border-0 mb-4 h-auto shadow-sm">
            <div class="card-header">
                <strong><i class="bi bi-database-fill-gear me-2"></i>ข้อมูลอ้างอิงจาก HOSxP (Reference Data)</strong>
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs mb-3" id="referenceTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="reference-dept-tab" data-bs-toggle="tab"
                            data-bs-target="#reference-dept" type="button" role="tab"
                            aria-controls="reference-dept" aria-selected="true">
                            <i class="bi bi-door-open me-1"></i>รายชื่อห้องตรวจ (Departments)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="reference-ward-tab" data-bs-toggle="tab"
                            data-bs-target="#reference-ward" type="button" role="tab"
                            aria-controls="reference-ward" aria-selected="false">
                            <i class="bi bi-hospital me-1"></i>รายชื่อหอผู้ป่วย (Wards)
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="referenceTabsContent">
                    <!-- HOSxP Departments Tab -->
                    <div class="tab-pane fade show active" id="reference-dept" role="tabpanel"
                        aria-labelledby="reference-dept-tab">
                        <div class="alert alert-warning py-2 small mb-2">
                            <i class="bi bi-info-circle-fill me-2"></i>ใช้ค่าจากคอลัมน์ <strong>"รหัสห้องตรวจ"</strong>
                            ไประบุในตั้งค่า OPD หรือ NCD
                        </div>
                        <div class="mb-3 d-flex justify-content-end">
                            <div class="input-group shadow-sm" style="max-width: 350px;">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                                <input type="text" id="deptSearch" class="form-control border-start-0"
                                    placeholder="ค้นหา รหัส หรือ ชื่อห้องตรวจ...">
                            </div>
                        </div>
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-sm table-bordered table-striped table-hover" id="deptTable">
                                <thead class="table-dark sticky-top">
                                    <tr>
                                        <th>รหัสห้องตรวจ</th>
                                        <th>ห้องตรวจ</th>
                                        <th>แผนก</th>
                                        <th class="text-center">สถานะ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($hosxp_departments as $dept)
                                        <tr>
                                            <td class="fw-bold text-primary">{{ $dept->depcode }}</td>
                                            <td>{{ $dept->department }}</td>
                                            <td>{{ $dept->spclty }}</td>
                                            <td class="text-center">
                                                @if ($dept->depcode_active == 'Y')
                                                    <span class="badge bg-success">ใช้งาน</span>
                                                @else
                                                    <span class="badge bg-secondary">ปิด</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- HOSxP Wards Tab -->
                    <div class="tab-pane fade" id="reference-ward" role="tabpanel" aria-labelledby="reference-ward-tab">
                        <div class="alert alert-warning py-2 small mb-2">
                            <i class="bi bi-info-circle-fill me-2"></i>ใช้ค่าจากคอลัมน์ <strong>"รหัส Ward"</strong>
                            ไประบุในตั้งค่า IPD
                        </div>
                        <div class="mb-3 d-flex justify-content-end">
                            <div class="input-group shadow-sm" style="max-width: 350px;">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                                <input type="text" id="wardSearch" class="form-control border-start-0"
                                    placeholder="ค้นหา รหัส หรือ ชื่อ Ward...">
                            </div>
                        </div>
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-sm table-bordered table-striped table-hover" id="wardTable">
                                <thead class="table-dark sticky-top">
                                    <tr>
                                        <th>รหัส Ward</th>
                                        <th>ชื่อ Ward</th>
                                        <th>แผนก</th>
                                        <th class="text-center">สถานะ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($hosxp_wards as $ward)
                                        <tr>
                                            <td class="fw-bold text-primary">{{ $ward->ward }}</td>
                                            <td>{{ $ward->name }}</td>
                                            <td>{{ $ward->spclty }}</td>
                                            <td class="text-center">
                                                @if ($ward->ward_active == 'Y')
                                                    <span class="badge bg-success">ใช้งาน</span>
                                                @else
                                                    <span class="badge bg-secondary">ปิด</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- End Card Reference Data -->
    </div><!-- End Main Container -->

    <div class="container-fluid mb-5 px-md-4">
        <!-- ================= NOTIFY URLs ================= -->
        <div class="card border-0 mb-5 h-auto shadow-sm">
            <div class="card-header">
                <strong><i class="bi bi-bell-fill me-2"></i>URL สำหรับแจ้งเตือน (Notify URLs)</strong>
            </div>
            <div class="card-body bg-light py-4">
                <div class="alert alert-warning py-2 small mb-4">
                    <i class="bi bi-info-circle-fill me-2"></i>ใช้ URL เหล่านี้ในการตั้งค่า <strong>Cron Job</strong>
                    หรือรันผ่าน Browser เพื่อกระตุ้นการส่งแจ้งเตือน Telegram ตามช่วงเวลาที่กำหนด
                </div>

                <div class="row g-3">
                    <!-- Column 1: เวรดึก -->
                    <div class="col-md-6 col-xl-3">
                        <div class="bg-white p-3 rounded shadow-sm border-top border-4 h-100"
                            style="border-top-color: #6f42c1 !important;">
                            <h6 class="fw-bold d-flex align-items-center mb-3" style="color: #6f42c1;">
                                <i class="bi bi-moon-stars-fill me-2"></i>สรุปเวรดึก (รัน 08.00 น.)
                            </h6>
                            <div class="space-y-3">
                                <div class="mb-3">
                                    <label class="form-label small text-muted mb-1">ER (อุบัติเหตุ-ฉุกเฉิน)</label>
                                    <div class="input-group input-group-sm shadow-sm">
                                        <input type="text" class="form-control bg-light border-end-0"
                                            value="{{ url('product/er_night_notify') }}" readonly>
                                        <button class="btn btn-outline-secondary border-start-0" type="button"
                                            onclick="copyToClipboard(this)"><i class="bi bi-clipboard"></i></button>
                                    </div>
                                </div>
                                <div>
                                    <label class="form-label small text-muted mb-1">IPD (ผู้ป่วยใน)</label>
                                    <div class="input-group input-group-sm shadow-sm">
                                        <input type="text" class="form-control bg-light border-end-0"
                                            value="{{ url('product/ipd_night_notify') }}" readonly>
                                        <button class="btn btn-outline-secondary border-start-0" type="button"
                                            onclick="copyToClipboard(this)"><i class="bi bi-clipboard"></i></button>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label class="form-label small text-muted mb-1">VIP (หอผู้ป่วยพิเศษ)</label>
                                    <div class="input-group input-group-sm shadow-sm">
                                        <input type="text" class="form-control bg-light border-end-0"
                                            value="{{ url('product/vip_night_notify') }}" readonly>
                                        <button class="btn btn-outline-secondary border-start-0" type="button"
                                            onclick="copyToClipboard(this)"><i class="bi bi-clipboard"></i></button>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label class="form-label small text-muted mb-1">LR (ห้องคลอด)</label>
                                    <div class="input-group input-group-sm shadow-sm">
                                        <input type="text" class="form-control bg-light border-end-0"
                                            value="{{ url('product/lr_night_notify') }}" readonly>
                                        <button class="btn btn-outline-secondary border-start-0" type="button"
                                            onclick="copyToClipboard(this)"><i class="bi bi-clipboard"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Column 2: เวรเช้า -->
                    <div class="col-md-6 col-xl-3">
                        <div class="bg-white p-3 rounded shadow-sm border-top border-4 h-100"
                            style="border-top-color: #28a745 !important;">
                            <h6 class="fw-bold d-flex align-items-center mb-3" style="color: #28a745;">
                                <i class="bi bi-sun-fill me-2"></i>สรุปเวรเช้า (รัน 16.00 น.)
                            </h6>
                            <div class="space-y-3">
                                <div class="mb-3">
                                    <label class="form-label small text-muted mb-1">ER (อุบัติเหตุ-ฉุกเฉิน)</label>
                                    <div class="input-group input-group-sm shadow-sm">
                                        <input type="text" class="form-control bg-light border-end-0"
                                            value="{{ url('product/er_morning_notify') }}" readonly>
                                        <button class="btn btn-outline-secondary border-start-0" type="button"
                                            onclick="copyToClipboard(this)"><i class="bi bi-clipboard"></i></button>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small text-muted mb-1">IPD (ผู้ป่วยใน)</label>
                                    <div class="input-group input-group-sm shadow-sm">
                                        <input type="text" class="form-control bg-light border-end-0"
                                            value="{{ url('product/ipd_morning_notify') }}" readonly>
                                        <button class="btn btn-outline-secondary border-start-0" type="button"
                                            onclick="copyToClipboard(this)"><i class="bi bi-clipboard"></i></button>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small text-muted mb-1">OPD (ผู้ป่วยนอก)</label>
                                    <div class="input-group input-group-sm shadow-sm">
                                        <input type="text" class="form-control bg-light border-end-0"
                                            value="{{ url('product/opd_morning_notify') }}" readonly>
                                        <button class="btn btn-outline-secondary border-start-0" type="button"
                                            onclick="copyToClipboard(this)"><i class="bi bi-clipboard"></i></button>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small text-muted mb-1">NCD (คลินิก NCD)</label>
                                    <div class="input-group input-group-sm shadow-sm">
                                        <input type="text" class="form-control bg-light border-end-0"
                                            value="{{ url('product/ncd_morning_notify') }}" readonly>
                                        <button class="btn btn-outline-secondary border-start-0" type="button"
                                            onclick="copyToClipboard(this)"><i class="bi bi-clipboard"></i></button>
                                    </div>
                                </div>
                                <div>
                                    <label class="form-label small text-muted mb-1">ARI (คลินิก ARI)</label>
                                    <div class="input-group input-group-sm shadow-sm">
                                        <input type="text" class="form-control bg-light border-end-0"
                                            value="{{ url('product/ari_morning_notify') }}" readonly>
                                        <button class="btn btn-outline-secondary border-start-0" type="button"
                                            onclick="copyToClipboard(this)"><i class="bi bi-clipboard"></i></button>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label class="form-label small text-muted mb-1">CKD (คลินิก CKD)</label>
                                    <div class="input-group input-group-sm shadow-sm">
                                        <input type="text" class="form-control bg-light border-end-0"
                                            value="{{ url('product/ckd_morning_notify') }}" readonly>
                                        <button class="btn btn-outline-secondary border-start-0" type="button"
                                            onclick="copyToClipboard(this)"><i class="bi bi-clipboard"></i></button>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label class="form-label small text-muted mb-1">HD (ฟอกเลือดด้วยเครื่องไตเทียม)</label>
                                    <div class="input-group input-group-sm shadow-sm">
                                        <input type="text" class="form-control bg-light border-end-0"
                                            value="{{ url('product/hd_morning_notify') }}" readonly>
                                        <button class="btn btn-outline-secondary border-start-0" type="button"
                                            onclick="copyToClipboard(this)"><i class="bi bi-clipboard"></i></button>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label class="form-label small text-muted mb-1">VIP (หอผู้ป่วยพิเศษ)</label>
                                    <div class="input-group input-group-sm shadow-sm">
                                        <input type="text" class="form-control bg-light border-end-0"
                                            value="{{ url('product/vip_morning_notify') }}" readonly>
                                        <button class="btn btn-outline-secondary border-start-0" type="button"
                                            onclick="copyToClipboard(this)"><i class="bi bi-clipboard"></i></button>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label class="form-label small text-muted mb-1">LR (ห้องคลอด)</label>
                                    <div class="input-group input-group-sm shadow-sm">
                                        <input type="text" class="form-control bg-light border-end-0"
                                            value="{{ url('product/lr_morning_notify') }}" readonly>
                                        <button class="btn btn-outline-secondary border-start-0" type="button"
                                            onclick="copyToClipboard(this)"><i class="bi bi-clipboard"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Column 3: เวรบ่าย -->
                    <div class="col-md-6 col-xl-3">
                        <div class="bg-white p-3 rounded shadow-sm border-top border-4 h-100"
                            style="border-top-color: #fd7e14 !important;">
                            <h6 class="fw-bold d-flex align-items-center mb-3" style="color: #fd7e14;">
                                <i class="bi bi-sunset-fill me-2"></i>สรุปเวรบ่าย (รัน 00.01 น.)
                            </h6>
                            <div class="space-y-3">
                                <div class="mb-3">
                                    <label class="form-label small text-muted mb-1">ER (อุบัติเหตุ-ฉุกเฉิน)</label>
                                    <div class="input-group input-group-sm shadow-sm">
                                        <input type="text" class="form-control bg-light border-end-0"
                                            value="{{ url('product/er_afternoon_notify') }}" readonly>
                                        <button class="btn btn-outline-secondary border-start-0" type="button"
                                            onclick="copyToClipboard(this)"><i class="bi bi-clipboard"></i></button>
                                    </div>
                                </div>
                                <div>
                                    <label class="form-label small text-muted mb-1">IPD (ผู้ป่วยใน)</label>
                                    <div class="input-group input-group-sm shadow-sm">
                                        <input type="text" class="form-control bg-light border-end-0"
                                            value="{{ url('product/ipd_afternoon_notify') }}" readonly>
                                        <button class="btn btn-outline-secondary border-start-0" type="button"
                                            onclick="copyToClipboard(this)"><i class="bi bi-clipboard"></i></button>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label class="form-label small text-muted mb-1">VIP (หอผู้ป่วยพิเศษ)</label>
                                    <div class="input-group input-group-sm shadow-sm">
                                        <input type="text" class="form-control bg-light border-end-0"
                                            value="{{ url('product/vip_afternoon_notify') }}" readonly>
                                        <button class="btn btn-outline-secondary border-start-0" type="button"
                                            onclick="copyToClipboard(this)"><i class="bi bi-clipboard"></i></button>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label class="form-label small text-muted mb-1">LR (ห้องคลอด)</label>
                                    <div class="input-group input-group-sm shadow-sm">
                                        <input type="text" class="form-control bg-light border-end-0"
                                            value="{{ url('product/lr_afternoon_notify') }}" readonly>
                                        <button class="btn btn-outline-secondary border-start-0" type="button"
                                            onclick="copyToClipboard(this)"><i class="bi bi-clipboard"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Column 4: เวร BD -->
                    <div class="col-md-6 col-xl-3">
                        <div class="bg-white p-3 rounded shadow-sm border-top border-4 h-100"
                            style="border-top-color: #17a2b8 !important;">
                            <h6 class="fw-bold d-flex align-items-center mb-3" style="color: #17a2b8;">
                                <i class="bi bi-clock-fill me-2"></i>สรุปเวร BD (รัน 20.00 น.)
                            </h6>
                            <div class="space-y-3">
                                <div>
                                    <label class="form-label small text-muted mb-1">OPD (ผู้ป่วยนอก - เวร BD)</label>
                                    <div class="input-group input-group-sm shadow-sm">
                                        <input type="text" class="form-control bg-light border-end-0"
                                            value="{{ url('product/opd_bd_notify') }}" readonly>
                                        <button class="btn btn-outline-secondary border-start-0" type="button"
                                            onclick="copyToClipboard(this)"><i class="bi bi-clipboard"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            /* =====================================================
               Git Pull
            ===================================================== */
            const gitBtn = document.getElementById('gitPullBtn');
            if (gitBtn) {
                gitBtn.addEventListener('click', function() {

                    Swal.fire({
                        title: 'ยืนยัน Git Pull?',
                        text: 'ระบบจะดึงโค้ดล่าสุดจาก Git',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'ใช่, ดำเนินการ',
                        cancelButtonText: 'ยกเลิก'
                    }).then((result) => {

                        if (!result.isConfirmed) return;

                        const outputBox = document.getElementById('gitOutput');
                        outputBox.textContent = 'กำลังดำเนินการ...';

                        fetch("{{ route('admin.git.pull') }}", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": document
                                        .querySelector('meta[name="csrf-token"]')
                                        .getAttribute('content')
                                }
                            })
                            .then(res => res.json())
                            .then(data => {

                                outputBox.textContent =
                                    data.output || data.error || 'ไม่มีข้อมูล';

                                if (
                                    data.output &&
                                    (
                                        data.output.includes('Updating') ||
                                        data.output.includes('Already up to date')
                                    )
                                ) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'สำเร็จ',
                                        text: 'Git Pull เรียบร้อย',
                                        timer: 1500,
                                        showConfirmButton: false
                                    });

                                    setTimeout(() => {
                                        window.location.href =
                                            "{{ route('admin.main_setting') }}";
                                    }, 2000);
                                }
                            })
                            .catch(err => {
                                outputBox.textContent = 'เกิดข้อผิดพลาด: ' + err;
                            });
                    });
                });
            }

            /* =====================================================
               HOSxP Reference Search
            ===================================================== */
            const deptSearch = document.getElementById('deptSearch');
            const deptTable = document.getElementById('deptTable');
            if (deptSearch && deptTable) {
                deptSearch.addEventListener('keyup', function() {
                    const value = this.value.toLowerCase();
                    const rows = deptTable.querySelectorAll('tbody tr');
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(value) ? '' : 'none';
                    });
                });
            }

            const wardSearch = document.getElementById('wardSearch');
            const wardTable = document.getElementById('wardTable');
            if (wardSearch && wardTable) {
                wardSearch.addEventListener('keyup', function() {
                    const value = this.value.toLowerCase();
                    const rows = wardTable.querySelectorAll('tbody tr');
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(value) ? '' : 'none';
                    });
                });
            }

        });


        /* =====================================================
           Upgrade Structure
        ===================================================== */
        function confirmUpgrade() {
            Swal.fire({
                title: 'ยืนยันการดำเนินการ?',
                text: 'คุณต้องการ Upgrade Structure หรือไม่?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'ใช่, ดำเนินการ!',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {

                    Swal.fire({
                        title: 'กำลังดำเนินการ...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    document.getElementById('structureForm').submit();
                }
            });
        }
    </script>

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ',
                text: @json(session('success')),
                timer: 2000,
                showConfirmButton: false
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'ผิดพลาด',
                text: @json(session('error')),
                showConfirmButton: true
            });
        </script>
    @endif
@endpush
