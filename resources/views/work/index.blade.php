@extends('layouts.admin')

@section('main-content')
<h1 class="h3 mb-4 text-gray-800">รายการงาน (Work List)</h1>

<div class="container">
    <table id="works-table" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ลำดับ</th>
                <th>สถานี</th>
                <th>ลูกค้า</th>
                <th>วันที่นัดหมาย</th>
                <th>ระยะทาง (กม.)</th>
                <th>สถานะ</th>
                <th>ผู้เกี่ยวข้อง</th>
                <th>จัดการ</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
@endsection

@section('scripts')
<!-- DataTables CSS & JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- ✅ DataTables AJAX -->
<script>
$(document).ready(function () {
    $('#works-table').DataTable({
        ajax: {
            url: window.APP_URL+"/api/works", // หรือ "/api/works" ถ้าไม่ได้ตั้งชื่อ route
            dataSrc: 'data'
        },
        columns: [
            { data: null, render: (data, type, row, meta) => meta.row + 1 },
            { data: 'station.StationName', defaultContent: '-' },
            { data: 'customer.CustomerName', defaultContent: '-' },
            { data: 'AppointmentDate' },
            { data: 'distance', render: d => d ? `${d} กม.` : '-' },
            { data: 'Status', render: s => s == 0 ? 'รอดำเนินการ' : 'เสร็จสิ้น' },
            { data: 'employees', render: function (data) {
                return data.map(e => e.team.name || '').join(', ');
            }},
            {
                    data: null,
                    render: function(data, type, row) {
                        return  '<a href="'+window.APP_URL+'stations/' + row.StationID + '/detail" class="btn btn-success btn-sm">ดูข้อมูลสถานี</a> |'+
                        '<a href="'+window.APP_URL+'stations/' + row.StationID + '/CheckDispenser" class="btn btn-info btn-sm">ตรวจสภาพตู้</a> |'+
                            '<a href="'+window.APP_URL+'works/' + row.WorkID + '/detail" class="btn btn-success btn-sm">บันทึกการตรวจ</a> ';
                    }
                }
        ],
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/th.json"
        }
    });
});
</script>
@endsection
