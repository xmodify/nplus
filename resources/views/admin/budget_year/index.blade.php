@extends('layouts.hnplus')

@section('content')
    <div class="container fade-in-up">

        <h3 class="mb-4 fw-bold text-dark"><i class="bi bi-calendar-check-fill me-2 text-primary"></i>ปีงบประมาณ</h3>
        <!-- ปุ่มเปิด Modal เพิ่ม -->
        <button class="btn btn-hnplus text-white mb-4 px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="bi bi-calendar-plus-fill me-2"></i>เพิ่มปีงบประมาณ
        </button>

        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="data">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-center ps-4">ปีงบประมาณ</th>
                                <th class="text-center">ชื่อปีงบประมาณ</th>
                                <th class="text-center">วันที่เริ่ม</th>
                                <th class="text-center">วันที่สิ้นสุด</th>
                                <th class="text-center pe-4" width="20%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (!empty($budget_year))
                                @foreach ($budget_year as $item)
                                    <tr>
                                        <td class="text-center ps-4 fw-bold text-primary">{{ $item->LEAVE_YEAR_ID }}</td>
                                        <td class="text-center">{{ $item->LEAVE_YEAR_NAME }}</td>
                                        <td class="text-center">{{ DateThai($item->DATE_BEGIN) }}</td>
                                        <td class="text-center">{{ DateThai($item->DATE_END) }}</td>
                                        <td class="text-center pe-4">
                                            <!-- ปุ่ม Edit -->
                                            <button type="button"
                                                class="btn btn-outline-warning btn-sm btn-edit px-3 rounded-pill"
                                                data-leave-year-id="{{ $item->LEAVE_YEAR_ID }}"
                                                data-leave-year-name="{{ $item->LEAVE_YEAR_NAME }}"
                                                data-date-begin="{{ $item->DATE_BEGIN }}"
                                                data-date-end="{{ $item->DATE_END }}" data-bs-toggle="modal"
                                                data-bs-target="#editModal">
                                                <i class="bi bi-pencil-fill me-1"></i>Edit
                                            </button>

                                            <!-- ปุ่ม Delete -->
                                            <form class="d-inline delete-form" method="POST"
                                                action="{{ route('admin.budget_year.destroy', $item) }}">
                                                @csrf @method('DELETE')
                                                <button type="button"
                                                    class="btn btn-outline-danger btn-sm btn-delete px-3 rounded-pill">
                                                    <i class="bi bi-trash-fill me-1"></i>Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Create -->
        <div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form method="POST" action="{{ route('admin.budget_year.store') }}"
                    class="modal-content border-0 shadow-lg">
                    @csrf
                    <div class="modal-header text-white" style="background: var(--primary-gradient);">
                        <h5 class="modal-title fw-bold"><i class="bi bi-calendar-plus-fill me-2"></i>เพิ่มปีงบประมาณ</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">ปีงบประมาณ</label>
                            <input class="form-control" name="LEAVE_YEAR_ID" type="text" placeholder="เช่น 2567"
                                required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">ชื่อปีงบประมาณ</label>
                            <input class="form-control" name="LEAVE_YEAR_NAME" type="text"
                                placeholder="เช่น ปีงบประมาณ 2567" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">วันที่เริ่ม</label>
                                <input class="form-control" name="DATE_BEGIN" type="date" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">วันที่สิ้นสุด</label>
                                <input class="form-control" name="DATE_END" type="date" required>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">ยกเลิก</button>
                        <button class="btn btn-primary px-4 fw-bold shadow-sm" type="submit">บันทึกข้อมูล</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Edit -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form method="POST" id="editForm" class="modal-content border-0 shadow-lg">
                    @csrf
                    @method('PUT')
                    <div class="modal-header text-white" style="background: var(--primary-gradient);">
                        <h5 class="modal-title fw-bold"><i class="bi bi-calendar-check-fill me-2"></i>แก้ไขปีงบประมาณ</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">ปีงบประมาณ</label>
                            <input class="form-control bg-light" id="eLEAVE_YEAR_ID" name="LEAVE_YEAR_ID" type="text"
                                readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">ชื่อปีงบประมาณ</label>
                            <input class="form-control" id="eLEAVE_YEAR_NAME" name="LEAVE_YEAR_NAME" type="text">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">วันที่เริ่ม</label>
                                <input class="form-control" id="eDATE_BEGIN" name="DATE_BEGIN" type="date">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">วันที่สิ้นสุด</label>
                                <input class="form-control" id="eDATE_END" name="DATE_END" type="date">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">ยกเลิก</button>
                        <button class="btn btn-primary px-4 fw-bold shadow-sm" type="submit">อัปเดตข้อมูล</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script type="text/javascript" language="javascript"
        src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" language="javascript"
        src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#data').DataTable({
                "language": {
                    "search": "ค้นหา:",
                    "lengthMenu": "แสดง _MENU_ รายการต่อหน้า",
                    "zeroRecords": "ไม่พบข้อมูล",
                    "info": "แสดงหน้า _PAGE_ จาก _PAGES_",
                    "infoEmpty": "ไม่มีข้อมูล",
                    "infoFiltered": "(กรองจากทั้งหมด _MAX_ รายการ)",
                    "paginate": {
                        "first": "หน้าแรก",
                        "last": "หน้าสุดท้าย",
                        "next": "ถัดไป",
                        "previous": "ก่อนหน้า"
                    }
                }
            });

            // ผูกอีเวนต์กับปุ่มทุกปุ่ม
            document.querySelectorAll('.btn-edit').forEach(btn => {
                btn.addEventListener('click', function() {
                    const leaveYearId = this.dataset.leaveYearId;
                    const leaveYearName = this.dataset.leaveYearName;
                    const dateBegin = this.dataset.dateBegin;
                    const dateEnd = this.dataset.dateEnd;

                    document.getElementById('eLEAVE_YEAR_ID').value = leaveYearId;
                    document.getElementById('eLEAVE_YEAR_NAME').value = leaveYearName;
                    document.getElementById('eDATE_BEGIN').value = dateBegin;
                    document.getElementById('eDATE_END').value = dateEnd;

                    document.getElementById('editForm').action =
                        `/admin/budget_year/${leaveYearId}`;
                });
            });

            // SweetAlert ยืนยันลบ
            document.querySelectorAll('.btn-delete').forEach(button => {
                button.addEventListener('click', function() {
                    const form = this.closest('form');
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "ต้องการลบปีงบประมาณนี้ใช่หรือไม่?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, delete it!'
                    }).then(result => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endpush
