@extends('layouts.admin')

@section('main-content')
<h1 class="h3 mb-4 text-gray-800">หัวจ่ายน้ำมันของสถานี {{ $station->StationName }}</h1>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        
        <div class="row">
            <div class="col-md-6"><h6 class="m-0 font-weight-bold text-primary">ตารางหัวจ่ายน้ำมัน</h6></div>
            <div class="col-md-6"><button class="btn btn-info">เพิ่มสถานี</button></div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dispenserTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>รหัสตู้จ่าย</th>
                        <th>จำนวนหัวจ่าย</th>
                        <th>ยี่ห้อ</th>
                        <th>วันที่ตรวจสอบล่าสุด</th>
                        <!-- <th>สถานะ</th> -->
                        <th>จัดการ</th> <!-- ✅ เพิ่มช่องนี้ -->
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
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#dispenserTable').DataTable({
            ajax: '{{env('APP_URL ')}}dispensers/data',
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    data: 'DispenserID'
                },
                // {
                //     data: 'FuelType'
                // },
                {
                    data: 'nozzles_count'
                },
                {
                    data: 'brand.BrandName'
                },
                {
                    data: 'LastCalibationDate'
                },
             
                {
                    data: null,
                    render: function(data, type, row, meta) {
                        return '<a href="/stations/' + row.DispenserID + '/nozzle" class="btn btn-info btn-sm">จัดการหัวจ่าย</a>';
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