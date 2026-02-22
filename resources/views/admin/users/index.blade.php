@extends('layouts.hnplus')

@section('content')
    <div class="container fade-in-up">

        <h3 class="mb-4 fw-bold text-dark"><i class="bi bi-people-fill me-2 text-primary"></i>User Management</h3>
        <!-- ปุ่มเปิด Modal เพิ่ม -->
        <button class="btn btn-hnplus text-white mb-4 px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="bi bi-person-plus-fill me-2"></i>Add User
        </button>

        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="data">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Name</th>
                                <th>Email</th>
                                <th class="text-center">Active</th>
                                <th class="text-center">Role</th>
                                <th class="text-center pe-4" width="20%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td class="ps-4 fw-bold text-dark">{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td class="text-center">
                                        @if ($user->active === 'Y')
                                            <span
                                                class="badge bg-success-subtle text-success border border-success-subtle px-3">Active</span>
                                        @else
                                            <span
                                                class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="badge bg-info-subtle text-info border border-info-subtle px-3 text-capitalize">{{ $user->role }}</span>
                                    </td>
                                    <td class="text-center pe-4">
                                        <!-- ปุ่ม Edit -->
                                        <button class="btn btn-outline-warning btn-sm btn-edit px-3 rounded-pill"
                                            data-id="{{ $user->id }}" data-name="{{ $user->name }}"
                                            data-email="{{ $user->email }}" data-active="{{ $user->active }}"
                                            data-role="{{ $user->role }}" data-bs-toggle="modal"
                                            data-bs-target="#editModal">
                                            <i class="bi bi-pencil-fill me-1"></i>Edit
                                        </button>

                                        <!-- ปุ่ม Delete -->
                                        <form class="d-inline delete-form" method="POST"
                                            action="{{ route('admin.users.destroy', $user) }}">
                                            @csrf @method('DELETE')
                                            <button type="button"
                                                class="btn btn-outline-danger btn-sm btn-delete px-3 rounded-pill">
                                                <i class="bi bi-trash-fill me-1"></i>Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Create -->
        <div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form method="POST" action="{{ route('admin.users.store') }}" class="modal-content border-0 shadow-lg">
                    @csrf
                    <div class="modal-header text-white" style="background: var(--primary-gradient);">
                        <h5 class="modal-title fw-bold"><i class="bi bi-person-plus-fill me-2"></i>Create User</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Name</label>
                            <input name="name" type="text" class="form-control" placeholder="Full Name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input name="email" type="email" class="form-control" placeholder="email@example.com"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Password</label>
                            <input name="password" type="password" class="form-control" placeholder="Password" required>
                        </div>
                        <input type="hidden" name="active" value="Y">
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-primary px-4 fw-bold shadow-sm">Save User</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Edit -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form method="POST" id="editForm" class="modal-content border-0 shadow-lg">
                    @csrf @method('PUT')
                    <div class="modal-header text-white" style="background: var(--primary-gradient);">
                        <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit User</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Name</label>
                            <input class="form-control" id="editName" name="name" type="text" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input class="form-control" id="editEmail" name="email" type="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Role</label>
                            <select class="form-select" id="editRole" name="role">
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="mb-3 d-flex align-items-center">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="active" id="editActive"
                                    value="Y">
                                <label class="form-check-label fw-bold ms-2" for="editActive">เปิดใช้งาน (Active)</label>
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-bold">New Password</label>
                            <input class="form-control" type="password" name="password"
                                placeholder="Leave blank to keep current password">
                        </div>
                    </div>

                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-primary px-4 fw-bold shadow-sm">Update User</button>
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

            // Set ข้อมูลใน Edit Modal
            document.querySelectorAll('.btn-edit').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = this.dataset.name;
                    const email = this.dataset.email;
                    const role = this.dataset.role;
                    const activeCheckbox = document.getElementById('editActive');
                    activeCheckbox.checked = (this.dataset.active === 'Y');

                    document.getElementById('editName').value = name;
                    document.getElementById('editEmail').value = email;
                    document.getElementById('editRole').value = role;
                    document.getElementById('editForm').action = `/admin/users/${id}`;
                });
            });

            // SweetAlert ยืนยันลบ
            document.querySelectorAll('.btn-delete').forEach(button => {
                button.addEventListener('click', function() {
                    const form = this.closest('form');
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
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
