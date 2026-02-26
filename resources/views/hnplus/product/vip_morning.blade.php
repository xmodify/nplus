<!doctype html>
<html lang="th">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>บันทึกผลิตภาพทางการพยาบาล VIP</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/beautiful_admin.css') }}">

    <style>
        body {
            background: radial-gradient(circle at top right, #f8f9fa, #e9ecef);
            font-family: 'Prompt', sans-serif;
            min-height: 100vh;
        }
    </style>
</head>

<body>
    <div class="container py-5">
        <div class="card-premium mx-auto shadow-lg"
            style="max-width: 650px; border: none; border-radius: 20px; overflow: hidden;">
            <div class="header-gradient text-white text-center py-4">
                <div class="mb-2">
                    <i class="bi bi-couch fs-1"></i>
                </div>
                <h4 class="mb-0 fw-bold">ระบบบันทึกผลิตภาพทางการพยาบาล</h4>
                <p class="mb-0 opacity-75">แผนกผู้ป่วยใน VIP | เวรเช้า</p>
            </div>

            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill">
                        <i class="bi bi-calendar3 me-2"></i>วันที่ {{ DateThai(date('Y-m-d')) }}
                    </span>
                    <div class="text-secondary small mt-2">
                        <i class="bi bi-sun me-1"></i> ช่วงเวลา 08.00-16.00 น.
                    </div>
                </div>

                <form id="productForm" action="{{ url('product/vip_morning_save') }}" method="POST">
                    @csrf
                    <input type="hidden" name="report_date" value="{{ date('Y-m-d') }}">
                    <input type="hidden" name="shift_time" value="เวรเช้า">

                    <div class="bg-light rounded-4 p-4 mb-4 border border-white shadow-sm">
                        <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">
                            <i class="bi bi-people-fill me-2 text-primary"></i>รายละเอียดจำนวนผู้ป่วย (VIP)
                        </h6>
                        @foreach ($shift as $row)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-secondary">จำนวนผู้ป่วยในเวร</span>
                                <span class="fw-bold text-dark fs-5">{{ $row->patient_all }} <small
                                        class="fw-normal text-muted">ราย</small></span>
                            </div>
                            <input type="hidden" name="patient_all" value="{{ $row->patient_all }}">

                            <div class="row g-2 mt-2">
                                <div class="col-6 col-md-4">
                                    <div class="p-2 border rounded bg-white text-center">
                                        <div class="small text-muted mb-1">Convalescent</div>
                                        <div class="fw-bold text-success">{{ $row->convalescent }}</div>
                                    </div>
                                    <input type="hidden" name="convalescent" value="{{ $row->convalescent }}">
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="p-2 border rounded bg-white text-center">
                                        <div class="small text-muted mb-1">Moderate</div>
                                        <div class="fw-bold text-info">{{ $row->Moderate }}</div>
                                    </div>
                                    <input type="hidden" name="Moderate" value="{{ $row->Moderate }}">
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="p-2 border rounded bg-white text-center">
                                        <div class="small text-muted mb-1">Semi Critical</div>
                                        <div class="fw-bold text-warning">{{ $row->Semi_critical }}</div>
                                    </div>
                                    <input type="hidden" name="Semi_critical" value="{{ $row->Semi_critical }}">
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="p-2 border rounded bg-white text-center">
                                        <div class="small text-muted mb-1">Critical</div>
                                        <div class="fw-bold text-danger">{{ $row->Critical }}</div>
                                    </div>
                                    <input type="hidden" name="Critical" value="{{ $row->Critical }}">
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="p-2 border rounded bg-white text-center">
                                        <div class="small text-muted mb-1">ไม่ระบุ</div>
                                        <div class="fw-bold text-secondary">{{ $row->severe_type_null }}</div>
                                    </div>
                                    <input type="hidden" name="severe_type_null" value="{{ $row->severe_type_null }}">
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mb-4">
                        <label for="nurse_fulltime" class="form-label text-secondary small fw-bold">
                            <i class="bi bi-person-check-fill me-1"></i>อัตรากำลังปกติ
                        </label>
                        <div class="input-group shadow-sm">
                            <input type="number" id="nurse_fulltime" name="nurse_fulltime"
                                class="form-control border-end-0 py-2" placeholder="ระบุจำนวน" step="any"
                                min="0">
                            <span class="input-group-text bg-white text-muted small">คน</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="nurse_partime" class="form-label text-secondary small fw-bold">
                            <i class="bi bi-person-add me-1"></i>อัตรากำลังเสริม
                        </label>
                        <div class="input-group shadow-sm">
                            <input type="number" id="nurse_partime" name="nurse_partime"
                                class="form-control border-end-0 py-2" placeholder="0" step="any"
                                min="0">
                            <span class="input-group-text bg-white text-muted small">คน</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="nurse_oncall" class="form-label text-secondary small fw-bold">
                            <i class="bi bi-person-plus me-1"></i>อัตรากำลัง Oncall
                        </label>
                        <div class="input-group shadow-sm">
                            <input type="number" id="nurse_oncall" name="nurse_oncall"
                                class="form-control border-end-0 py-2" placeholder="0" step="any"
                                min="0">
                            <span class="input-group-text bg-white text-muted small">คน</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="recorder" class="form-label text-secondary small fw-bold">
                            <i class="bi bi-pencil-square me-1"></i>ผู้บันทึก
                        </label>
                        <input type="text" id="recorder" name="recorder" class="form-control py-2 shadow-sm"
                            placeholder="ชื่อ-นามสกุล">
                    </div>

                    <div class="mb-4">
                        <label for="note" class="form-label text-secondary small fw-bold">
                            <i class="bi bi-chat-left-text me-1"></i>หมายเหตุ
                        </label>
                        <textarea id="note" name="note" class="form-control py-2 shadow-sm" rows="2"
                            placeholder="ข้อมูลเพิ่มเติม (ถ้ามี)"></textarea>
                    </div>

                    <div class="d-grid gap-2 mt-5">
                        <button type="submit"
                            class="btn btn-primary btn-lg fw-bold py-3 shadow border-0 rounded-pill">
                            <i class="bi bi-send-fill me-2"></i>ส่งข้อมูล
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('productForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const fields = [{
                    id: 'nurse_fulltime',
                    label: 'อัตรากำลังปกติ'
                },
                {
                    id: 'nurse_partime',
                    label: 'อัตรากำลังเสริม'
                },
                {
                    id: 'nurse_oncall',
                    label: 'อัตรากำลัง Oncall'
                },
                {
                    id: 'recorder',
                    label: 'ผู้บันทึก'
                }
            ];

            let missingFields = [];
            fields.forEach(field => {
                const element = document.getElementById(field.id);
                if (!element.value.trim()) {
                    missingFields.push(field.label);
                }
            });

            if (missingFields.length > 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'กรอกข้อมูลไม่ครบ',
                    html: `กรุณากรอก: <b>${missingFields.join(', ')}</b>`,
                    confirmButtonText: 'ตกลง',
                    confirmButtonColor: '#23A7A7'
                });
                return;
            }

            Swal.fire({
                title: 'ยืนยันการบันทึก?',
                text: "ตรวจสอบข้อมูลให้ถูกต้องก่อนส่ง (VIP)",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'บันทึกข้อมูล',
                cancelButtonText: 'ยกเลิก',
                confirmButtonColor: '#23A7A7'
            }).then((result) => {
                if (result.isConfirmed) {
                    e.target.submit();
                }
            });
        });
    </script>

    @if (session('success'))
        <script>
            Swal.fire({
                title: 'บันทึกสำเร็จ!',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonText: 'ตกลง'
            });
        </script>
    @endif

    @if ($errors->any())
        <script>
            Swal.fire({
                title: 'ข้อมูลไม่ถูกต้อง!',
                text: 'กรุณาตรวจสอบข้อมูลที่กรอกอีกครั้ง',
                icon: 'error',
                confirmButtonText: 'ตกลง'
            });
        </script>
    @endif
</body>

</html>
