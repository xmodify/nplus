<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ระบบบันทึกผลิตภาพทางการพยาบาล</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap">

    <style>
        body {
            background: #f4f7fa;
            font-family: "Prompt", sans-serif;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        .card-header {
            background-color: #23A7A7;
        }
        .form-label {
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="container py-4">
        <div class="card mx-auto" style="max-width: 700px;">

            <!-- Header -->
            <div class="card-header text-white text-center">
                <h5 class="mb-0">
                    <strong>ระบบบันทึกผลิตภาพทางการพยาบาล<br>แผนกผู้ป่วยนอก OPD<br>เวรเช้า</strong>
                </h5>
            </div>

            <div class="card-body">

                <h6 class="text-primary text-center mb-3">
                    วันที่ {{ DateThai(date('Y-m-d')) }} <br> ช่วงเวลา 08.00–16.00 น.
                </h6>

                <form id="opdForm" action="{{ url('hnplus/product/opd_morning_save') }}" method="POST">
                    @csrf
                    <input type="hidden" name="report_date" value="{{ date('Y-m-d') }}">
                    <input type="hidden" name="shift_time" value="เวรเช้า">

                    <!-- ============================
                         แสดงจำนวนผู้ป่วยจาก $visit
                    ============================ -->
                    @foreach($shift as $row)

                    <div class="row mb-2">
                        <div class="col-8">จำนวนผู้ป่วยทั้งหมดในเวร</div>
                        <div class="col text-end"><strong>{{ $row->patient_all }}</strong> ราย</div>
                    </div>
                    <input type="hidden" name="patient_all" value="{{ $row->patient_all }}">

                    <div class="row mb-2">
                        <div class="col-8">จำนวนผู้ป่วย OPD</div>
                        <div class="col text-end"><strong>{{ $row->opd }}</strong> ราย</div>
                    </div>
                    <input type="hidden" name="opd" value="{{ $row->opd }}">

                    <div class="row mb-4">
                        <div class="col-8">จำนวนผู้ป่วย ARI</div>
                        <div class="col text-end"><strong>{{ $row->ari }}</strong> ราย</div>
                    </div>
                    <input type="hidden" name="ari" value="{{ $row->ari }}">

                    @endforeach

                    <!-- ============================
                         ช่องกรอกข้อมูลอัตรากำลัง
                    ============================ -->
                    <div class="mb-3">
                        <label class="form-label">อัตรากำลัง Oncall (ไม่มีใส่ 0)</label>
                        <input type="number" name="nurse_oncall" id="nurse_oncall" class="form-control" placeholder="ระบุจำนวน">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">อัตรากำลังเสริม (ไม่มีใส่ 0)</label>
                        <input type="number" name="nurse_partime" id="nurse_partime" class="form-control" placeholder="ระบุจำนวน">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">อัตรากำลังปกติ</label>
                        <input type="number" name="nurse_fulltime" id="nurse_fulltime" class="form-control" placeholder="ระบุจำนวน">
                    </div>

                    <!-- ============================
                         ผู้บันทึก + หมายเหตุ
                    ============================ -->
                    <div class="mb-3">
                        <label class="form-label">ผู้บันทึก</label>
                        <input type="text" name="recorder" id="recorder" class="form-control" placeholder="ชื่อ-สกุล ผู้บันทึก">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">หมายเหตุ</label>
                        <input type="text" name="note" class="form-control" placeholder="หมายเหตุเพิ่มเติม (ถ้ามี)">
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn text-white px-4" style="background-color:#23A7A7;">ส่งข้อมูล</button>
                        <button type="reset" class="btn btn-secondary px-4">ล้างข้อมูล</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<!-- ============================
     SWEETALERT ตรวจสอบก่อนส่ง
============================ -->
<script>
    document.getElementById('opdForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const nurse_oncall = document.getElementById('nurse_oncall').value.trim();
        const nurse_partime = document.getElementById('nurse_partime').value.trim();
        const nurse_fulltime = document.getElementById('nurse_fulltime').value.trim();
        const recorder = document.getElementById('recorder').value.trim();

        if (!nurse_oncall || !nurse_partime || !nurse_fulltime || !recorder) {
            Swal.fire({
                icon: 'warning',
                title: 'กรอกข้อมูลไม่ครบ',
                text: 'กรุณากรอกข้อมูลให้ครบทุกช่องก่อนส่ง!',
                confirmButtonText: 'ตกลง'
            });
            return;
        }

        Swal.fire({
            title: 'ยืนยันการบันทึก?',
            text: "กรุณาตรวจสอบข้อมูลก่อนบันทึก",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'บันทึกข้อมูล',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                e.target.submit();
            }
        });
    });
</script>

<!-- หากบันทึกสำเร็จ -->
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

</body>
</html>
