@extends('layouts.admin')

@section('main-content')
<h1 class="h3 mb-4 text-gray-800">รายชื่อสถานีน้ำมัน</h1>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">ตารางสถานีน้ำมัน</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="stationTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>รหัสสถานี</th>
                        <th>ชื่อสถานี</th>
                        <th>ที่ตั้ง</th>
                        <th>จังหวัด</th>
                        <th>ผู้ติดต่อ</th>
                        <th>เบอร์โทร</th>
                        <th>จัดการหัวจ่าย</th> <!-- เพิ่มคอลัมน์นี้ -->
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#stationTable').DataTable({
        ajax: '{{env('APP_URL')}}stations/data',
        columns: [
            { data: null, render: function (data, type, row, meta) {
                return meta.row + 1;
            }},
            { data: 'StationCode' },
            { data: 'StationName' },
            { data: 'Location' },
            { data: 'Province' },
            { data: 'ContactPerson' },
            { data: 'Phone' },
            {
                data: null, 
                render: function (data, type, row, meta) {
                    return '<a href="/stations/' + row.StationID + '/dispensers" class="btn btn-info btn-sm">จัดการหัวจ่าย</a>';
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
