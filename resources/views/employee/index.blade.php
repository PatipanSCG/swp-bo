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
            <table id="employeeTable"  width="100%"class="table table-bordered">
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
@endsection

@section('scripts')
<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

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
                        return '<a href="/employees/' + row.EmpCode + '/edit" class="btn btn-info btn-sm">แก้ไข</a>';
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
</script>
@endsection