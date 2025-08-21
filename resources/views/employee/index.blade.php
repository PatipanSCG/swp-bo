@extends('layouts.admin')

@section('main-content')
<h1 class="h3 mb-4 text-gray-800">📋 รายชื่อพนักงานทั้งหมด</h1>

@if (session('success'))
<div class="alert alert-success border-left-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">รายชื่อพนักงาน</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="employeeTable" width="100%" class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>รหัสพนักงาน</th>
                        <th>ชื่อเล่น</th>
                        <th>ชื่อ (TH)</th>
                        <th>ชื่อ (EN)</th>
                        <th>อีเมล</th>
                        <th>ตำแหน่ง</th>
                        <th>เบอร์โทร</th>
                        <th>มือถือ</th>
                        <th>การจัดการ</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="editEmployeeModal" tabindex="-1" role="dialog" aria-labelledby="editEmployeeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="editEmployeeForm" method="POST" action="">
            @csrf
        
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editEmployeeModalLabel">เพิ่มข้อมูลพนักงานเข้าสู่ระบบ SWP</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="EmpCode" id="editEmpCode">
                    <div class="form-group">
                        <label>ชื่อเล่น</label>
                        <input type="text" class="form-control" name="NickName" id="editNickName">
                    </div>
                    <div class="form-group">
                        <label>ชื่อไทย</label>
                        <input type="text" class="form-control" name="NameTH" id="editNameTH">
                    </div>
                    <div class="form-group">
                        <label>ชื่ออังกฤษ</label>
                        <input type="text" class="form-control" name="NameEN" id="editNameEN">
                    </div>
                    <div class="form-group">
                        <label>ตำแหน่ง</label>
                        <input type="text" class="form-control" name="Position" id="editPosition">
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" class="form-control" name="Username" id="editusername">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                    <button type="submit" class="btn btn-primary">บันทึกการเปลี่ยนแปลง</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>


<script>
    $(document).ready(function() {
        $('#employeeTable').DataTable({
            ajax: '{{env('APP_URL')}}employees/data',
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    data: 'EmpCode'
                },
                {
                    data: 'NickName'
                },
                {
                    data: 'NameTH'
                },
                {
                    data: 'NameEN'
                },
                {
                    data: 'Email'
                },
                {
                    data: 'Position'
                },
                {
                    data: 'InternalPhone'
                },
                {
                    data: 'Mobile'
                },

                {
                    data: null,
                    render: function(data, type, row, meta) {
                        let statusText = (row.SWPis == 0) ? '❌ ยังไม่อยู่ในระบบ SWP' : '✅ อยู่ในระบบ SWP';

                        let button = '';
                        if (row.SWPis == 0) {
                            button = '<button class="btn btn-info btn-sm open-edit-modal" ' +
                                'data-empcode="' + row.EmpCode + '" ' +
                                'data-nickname="' + row.NickName + '" ' +
                                'data-nameth="' + row.NameTH + '" ' +
                                'data-nameen="' + row.NameEN + '" ' +
                                'data-position="' + row.Position + '" ' +
                                'data-username="' + row.UserName + '">' +
                                'เพิ่มเข้าระบบ SWP</button>';
                        }

                        return statusText + '<br>' + button;
                    }
                }
            ],
            language: {
                search: "ค้นหา:",
                lengthMenu: "แสดง _MENU_ รายการต่อหน้า",
                zeroRecords: "ไม่พบข้อมูล",
                info: "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
                infoEmpty: "ไม่มีข้อมูล",
                infoFiltered: "(ค้นหาจากทั้งหมด _MAX_ รายการ)",
                paginate: {
                    first: "หน้าแรก",
                    last: "หน้าสุดท้าย",
                    next: "ถัดไป",
                    previous: "ย้อนกลับ"
                }
            }
        });
    });
    $(document).on('click', '.open-edit-modal', function() {
        const empCode = $(this).data('empcode');
        const nickName = $(this).data('nickname');
        const nameTH = $(this).data('nameth');
        const nameEN = $(this).data('nameen');
        const position = $(this).data('position');
        const username = $(this).data('username');

        // แสดงใน console
        console.log("EmpCode:", empCode);
        console.log("NickName:", nickName);
        console.log("NameTH:", nameTH);
        console.log("NameEN:", nameEN);
        console.log("Position:", position);
        console.log("username:", username);

        // ถ้าจะใส่ข้อมูลลงใน modal หรือ form:
        $('#editEmpCode').val(empCode);
        $('#editNickName').val(nickName);
        $('#editNameTH').val(nameTH);
        $('#editNameEN').val(nameEN);
        $('#editPosition').val(position);
        $('#editusername').val(username);

        // ตั้งค่า action URL ของ form เช่น /employees/{EmpCode}
        $('#editEmployeeForm').attr('action', '/employees/addemployee/' + empCode);

        $('#editEmployeeModal').modal('show');
    });
</script>
@endsection