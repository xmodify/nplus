@extends('layouts.hnplus')

@section('content')
    <style>
        .table-premium thead th {
            background-color: #ebf5ff !important;
            color: #0065ff !important;
            border-bottom: 2px solid #dbeafe !important;
        }

        .bg-soft-blue {
            background-color: #ebf5ff !important;
            color: #0065ff !important;
        }
    </style>
    <div class="container-fluid mb-4">
        <div class="card-premium p-3 shadow-sm border-0 bg-white">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center">
                    <div class="bg-soft-info p-2 rounded-3 me-3">
                        <i class="bi bi-heart-pulse fs-4 text-info"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">รายงานผลิตภาพทางการพยาบาลแผนกผู้ป่วย CKD</h5>
                        <small class="text-muted">ข้อมูลสรุปช่วงเวลา {{ DateThai($start_date) }} -
                            {{ DateThai($end_date) }}</small>
                    </div>
                </div>

                <form method="POST" class="d-flex align-items-center gap-2">
                    @csrf
                    <div class="input-group input-group-date-custom shadow-sm">
                        <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                        <input type="text" name="start_date" class="form-control datepicker-thai text-center"
                            value="{{ DateThai($start_date) }}" placeholder="เช่น 1 ม.ค. 2569">
                    </div>
                    <span class="text-muted small">ถึง</span>
                    <div class="input-group input-group-date-custom shadow-sm">
                        <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                        <input type="text" name="end_date" class="form-control datepicker-thai text-center"
                            value="{{ DateThai($end_date) }}" placeholder="เช่น 31 ม.ค. 2569">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm fw-bold px-3 shadow-sm rounded-3 py-2">
                        <i class="bi bi-search me-1"></i>ค้นหา
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Summary Table -->
    <div class="container-fluid mb-5">
        <div class="card-premium shadow-lg">
            <div
                class="header-gradient text-white p-2 p-md-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-graph-up-arrow me-2"></i>รายงานสรุปผลิตภาพทางการพยาบาล
                    <small class="opacity-75 ms-2 d-none d-lg-inline-block">({{ DateThai($start_date) }} -
                        {{ DateThai($end_date) }})</small>
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 table-premium">
                        <thead class="bg-soft-blue">
                            <tr>
                                <th class="text-center">เวร</th>
                                <th class="text-center">ผู้ป่วยในเวร</th>
                                <th class="text-center">ชม.การพยาบาล</th>
                                <th class="text-center">อัตรากำลัง Oncall</th>
                                <th class="text-center">อัตรากำลังเสริม</th>
                                <th class="text-center">อัตรากำลังปกติ</th>
                                <th class="text-center">ชม.การทำงาน</th>
                                <th class="text-center">Productivity</th>
                                <th class="text-center">NHPPD</th>
                                <th class="text-center">พยาบาลที่ต้องการ</th>
                            </tr>
                        </thead>
                        @foreach ($product_summary as $row)
                            <tr>
                                <td align="right">{{ $row->shift_time }} {{ $row->shift_time_sum }} เวร</td>
                                <td align="right">{{ $row->patient_all }}</td>
                                <td align="right">{{ number_format($row->patient_hr, 2) }}</td>
                                <td align="right">{{ $row->nurse_oncall }}</td>
                                <td align="right">{{ $row->nurse_partime }}</td>
                                <td align="right">{{ $row->nurse_fulltime }}</td>
                                <td align="right">{{ number_format($row->nurse_hr, 2) }}</td>
                                <td align="right">{{ number_format($row->productivity, 2) }}</td>
                                <td align="right">{{ number_format($row->nhppd, 2) }}</td>
                                <td align="right">{{ number_format($row->nurse_shift_time, 2) }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
    <br>

    <!-- Detail Table -->
    <div class="container-fluid mb-5">
        <div class="card-premium shadow-lg">
            <div class="header-gradient text-white p-3">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-list-check me-2"></i>การบันทึกข้อมูลผลิตภาพรายเวร
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="row mb-3">
                    @if ($message = Session::get('danger'))
                        <div class="alert alert-danger border-0 shadow-sm text-center">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i><strong>{{ $message }}</strong>
                        </div>
                    @endif
                </div>

                <div class="table-responsive">
                    <table id="productivity_ckd" class="table table-hover align-middle table-premium">
                        <thead class="bg-soft-blue">
                            <tr>
                                <th class="text-center">ลำดับ</th>
                                <th class="text-center">วันที่</th>
                                <th class="text-center">เวร</th>
                                <th class="text-center">ผู้ป่วยในเวร</th>
                                <th class="text-center">ชม.การพยาบาล</th>
                                <th class="text-center">อัตรากำลัง Oncall</th>
                                <th class="text-center">อัตรากำลังเสริม</th>
                                <th class="text-center">อัตรากำลังปกติ</th>
                                <th class="text-center">ชม.การทำงาน</th>
                                <th class="text-center">Productivity</th>
                                <th class="text-center">NHPPD</th>
                                <th class="text-center">พยาบาลที่ต้องการ</th>
                                <th class="text-center">ผู้บันทึก</th>
                                <th class="text-center">หมายเหตุ</th>
                                @if ($del_product)
                                    <th class="text-center">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <?php $count = 1; ?>
                        @foreach ($product as $row)
                            <tr>
                                <td align="right">{{ $count }}</td>
                                <td align="right">{{ DateThai($row->report_date) }}</td>
                                <td align="right">{{ $row->shift_time }}</td>
                                <td align="right">{{ $row->patient_all }}</td>
                                <td align="right">{{ number_format($row->patient_hr, 2) }}</td>
                                <td align="right">{{ $row->nurse_oncall }}</td>
                                <td align="right">{{ $row->nurse_partime }}</td>
                                <td align="right">{{ $row->nurse_fulltime }}</td>
                                <td align="right">{{ number_format($row->nurse_hr, 2) }}</td>
                                <td align="right">{{ number_format($row->productivity, 2) }}</td>
                                <td align="right">{{ number_format($row->nhppd, 2) }}</td>
                                <td align="right">{{ number_format($row->nurse_shift_time, 2) }}</td>
                                <td align="right">{{ $row->recorder }}</td>
                                <td align="right">{{ $row->note }}</td>
                                @if ($del_product)
                                    <td class="text-center">
                                        <form action="{{ url('product/ckd_product_delete/' . $row->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('ต้องการลบข้อมูล {{ DateThai($row->report_date) }} {{ $row->shift_time }} Product {{ number_format($row->productivity, 2) }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                            <?php $count++; ?>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
    <br>

    <!-- กราฟ Productivity -->
    <div class="container-fluid mb-5">
        <div class="card-premium shadow-lg">
            <div class="header-gradient text-white p-3">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-bar-chart-fill me-2"></i>กราฟ Productivity แยกตามเวร
                </h6>
            </div>
            <div class="card-body p-4">
                <div style="height: 400px;">
                    <canvas id="productivity_chart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <br>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.3/js/buttons.html5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

    <script>
        $(document).ready(function() {
            var table = $('#productivity_ckd').DataTable({
                dom: "<'row mb-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'fB>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                buttons: [{
                    extend: 'excelHtml5',
                    text: '<i class="bi bi-file-earmark-excel me-1"></i> Excel',
                    className: 'btn btn-success btn-sm ms-2 shadow-sm fw-bold',
                    title: 'รายงานผลิตภาพทางการพยาบาล_CKD_{{ $start_date }}_{{ $end_date }}'
                }],
                language: {
                    search: "ค้นหา: ",
                    lengthMenu: "แสดง _MENU_ ",
                    info: "รายการที่ _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
                    paginate: {
                        first: "หน้าแรก",
                        last: "หน้าสุดท้าย",
                        next: "ถัดไป",
                        previous: "ก่อนหน้า"
                    }
                }
            });
        });

        // 📊 กราฟ Productivity แยกตามเวร
        document.addEventListener("DOMContentLoaded", () => {
            const ctx = document.querySelector('#productivity_chart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode($report_date); ?>,
                        datasets: [{
                            label: 'เวรเช้า',
                            data: <?php echo json_encode($morning); ?>,
                            backgroundColor: 'rgba(35, 167, 167, 0.7)',
                            borderColor: 'rgb(35, 167, 167)',
                            borderRadius: 6,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            datalabels: {
                                anchor: 'end',
                                align: 'top',
                                color: '#000',
                                font: {
                                    weight: 'bold'
                                },
                                formatter: (value) => value
                            },
                            legend: {
                                position: 'top'
                            },
                            title: {
                                display: true,
                                text: 'กราฟ Productivity แยกตามเวร'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    },
                    plugins: [ChartDataLabels]
                });
            }
        });
    </script>
@endpush
