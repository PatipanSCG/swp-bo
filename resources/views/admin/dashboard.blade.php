@extends('layouts.admin')

@section('main-content')
<div class="row mb-2">
    <div class="col-md-3">
        <select id="searchType" class="form-control">
            <option value="tax">ค้นหาด้วยเลขผู้เสียภาษี</option>
            <option value="name">ค้นหาด้วยชื่อสถานี</option>
            <option value="phone">ค้นหาด้วยหมายเลขโทรศัพท์</option>
        </select>
    </div>
    <div class="col-md-5">
        <input type="text" id="searchInput" class="form-control" placeholder="พิมพ์คำค้นหา...">
    </div>
    <div class="col-md-2">
        <button class="btn btn-primary" id="searchBtn">🔍 ค้นหา</button>
    </div>
    <div class="col-md-2">
        <button class="btn btn-primary" id="searchBtn">เพิ่มปั้ม</button>
    </div>
</div>
<div class="row" id="station-container">
    <div class="text-center">⏳ กำลังโหลดข้อมูล...</div>
</div>

@endsection

@section('scripts')
<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>


<script>
   let stationData = [];

function loadStations(type = '', keyword = '') {
    $('#station-container').html('<div class="text-center">⏳ กำลังโหลดข้อมูล...</div>');

    $.ajax({
        url: `{{ env("APP_URL") }}stations/data`,
        data: {
            type: type,
            keyword: keyword,
            limit:20
        },
        success: function (data) {
            stationData = data.data;
            renderStations(stationData);
        },
        error: function () {
            $('#station-container').html('<div class="text-danger text-center">❌ โหลดข้อมูลล้มเหลว</div>');
        }
    });
}

$(document).ready(function () {
    loadStations(); // โหลดครั้งแรก

    $('#searchBtn').on('click', function () {
        const type = $('#searchType').val();
        const keyword = $('#searchInput').val().trim();
        loadStations(type, keyword);
    });
});
function renderStations(data) {
    console.log(data);
    let html = '';
    let now = new Date();

    data.sort((a, b) => new Date(a.latest_calibration_date) - new Date(b.latest_calibration_date))
        .forEach(function (station) {
            console.log(station)
            let expiredDate = new Date(station.latest_calibration_date);
            let diffDays = Math.ceil((expiredDate - now) / (1000 * 60 * 60 * 24));

            let color = 'success';
            if (diffDays < 0) color = 'danger';
            else if (diffDays <= 15) color = 'danger';
            else if (diffDays <= 30) color = 'warning';
            else if (diffDays <= 60) color = 'info';

           const subdistrictName = station.subdistrict?.NameInThai || '-';
    const districtName = station.district?.NameInThai || '-';
    const provinceName = station.province?.NameInThai || '-';

    html += `
        <div class="col-md-3 mb-4">
            <div class="card border-${color} text-${color}">
                <div class="card-header bg-${color} text-white"><h5>${station.StationName}</h5></div>
                <div class="card-body">
                    <p>จำนวนหัวจ่าย: ${station.nozzle_count}</p>
                    <p>วันหมดอายุ: ${formatDate(expiredDate)}</p>
                    <p>ที่อยู่: ต.${subdistrictName} อ.${districtName} จ.${provinceName}</p>
                </div>
                <div class="card-footer">
                    <a taget="_blank" href="/stations/${station.StationID}/detail" class="btn btn-success btn-sm">ดูข้อมูล</a>
                </div>
            </div>
        </div>`;
});

    $('#station-container').html(html);
}
function formatDate(dateObj) {
    const day = String(dateObj.getDate()).padStart(2, '0');
    const month = String(dateObj.getMonth() + 1).padStart(2, '0');
    const year = dateObj.getFullYear();
    return `${day}/${month}/${year}`;
}
</script>
@endsection