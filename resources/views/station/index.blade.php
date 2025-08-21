@extends('layouts.admin')
@section('main-content')
<h1 class="h3 mb-4 text-gray-800">รายชื่อสถานีน้ำมัน</h1>

<div class="card shadow mb-4">
    <div class="card-header py-3">

        <div class="row">
            <div class="col-md-6">
                <h6 class="m-0 font-weight-bold text-primary">ตารางสถานีน้ำมัน</h6>
            </div>
            <div class="col-md-6 text-right"><button class="btn btn-info" id="btn-addstation">เพิ่มสถานี</button></div>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-2">
                <input type="text" id="filter-name" class="form-control" placeholder="ชื่อสถานี">
            </div>
            <div class="col-md-2">
                <input type="text" id="filter-taxid" class="form-control" placeholder="เลขผู้เสียภาษี">
            </div>
            <div class="col-md-2">
                <select id="filter-brand" class="form-control">
                    <option value="">-- แบรนด์ --</option>
                    @foreach($brands as $brand)
                    <option value="{{ $brand->BrandID }}">{{ $brand->BrandName }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select id="filter-province" class="form-control">
                    <option value="">-- จังหวัด --</option>
                    @foreach($provinces as $prov)
                    <option value="{{ $prov->NameInThai }}">{{ $prov->NameInThai }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <button class="btn btn-primary" id="btn-filter">ค้นหา</button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered" id="stationTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>ชื่อสถานี</th>
                        <th>แบรนด์</th>
                        <th>ที่อยู่</th>
                        <th>จังหวัด</th> <!-- เพิ่มคอลัมน์นี้ -->
                        <th>อำเภอ</th>
                        <th>ตำบล</th>
                        <th>วันที่หมดอายุ</th> <!-- เพิ่มคอลัมน์นี้ -->
                        <th>จำนวนตู้จ่าย/จำนวนหัวจ่าย</th> <!-- เพิ่มคอลัมน์นี้ -->
                        <th>จัดการ</th> <!-- เพิ่มคอลัมน์นี้ -->
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<!-- Add Station Modal -->
<div class="modal fade" id="addStationModal" tabindex="-1" role="dialog" aria-labelledby="addStationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form id="addStationForm" methon="post">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">เพิ่มสถานีใหม่</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label>ชื่อสถานี</label>
                        <input type="text" class="form-control" name="StationName" required>
                    </div>

                    <div class="form-group">
                        <label>เลขผู้เสียภาษี</label>
                        <input type="text" class="form-control" name="TaxID">
                    </div>

                    <div class="form-group">
                        <label>ยี่ห้อ</label>
                        <select class="form-control" name="BrandID" required>
                            @foreach($brands as $brand)
                            <option value="{{ $brand->BrandID }}">{{ $brand->BrandName }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>ที่อยู่</label>
                        <input type="text" class="form-control" name="Address">
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>จังหวัด</label>
                            <select class="form-control" name="Province" id="ip-province">
                                @foreach($provinces as $prov)
                                <option value="{{ $prov->Id }}">{{ $prov->NameInThai }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label>อำเภอ</label>
                            <select class="form-control" name="District" id="ip-district"></select>
                        </div>
                        <div class="form-group col-md-4">
                            <label>ตำบล</label>
                            <select class="form-control" name="Subdistrict" id="ip-subdistrict"></select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>รหัสไปรษณีย์</label>
                        <input type="text" class="form-control" name="Postcode" id="ip-postcode">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">บันทึก</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>


<!-- ✅ Popper.js (ถ้าใช้ Bootstrap 4) -->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>

<!-- ✅ Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>



<script>
    $(document).ready(function() {
        $('#stationTable').DataTable({
            ajax: {
                url: window.APP_URL+'/stations/data',
                data: function(d) {
                    d.name = $('#filter-name').val();
                    d.taxid = $('#filter-taxid').val();
                    d.province = $('#filter-province').val();
                    d.brand = $('#filter-brand').val();
                }
            },
            rowId: 'StationID',
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    data: 'StationName'
                },
                {
                    data: 'brand.BrandName'
                },
                {
                    data: 'Address'
                },
                {
                    data: 'province.NameInThai'
                },
                {
                    data: 'district.NameInThai'
                },
                {
                    data: 'subdistrict.NameInThai'
                },
                {
                    data: 'latest_calibration_date'
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return row.dispensers_count + ' / ' + row.nozzle_count;
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return '<a href="'+window.APP_URL+'/stations/' + row.StationID + '/dispensers" class="btn btn-info btn-sm">จัดการตู้จ่าย</a> | ' +
                            '<a href="'+window.APP_URL+'/stations/' + row.StationID + '/detail" class="btn btn-success btn-sm">ดูข้อมูล</a>';
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

        $('#btn-addstation').on('click', function() {

            $('#addStationModal').modal('show');

        });

        $('#btn-filter').on('click', function() {
            
            $('#stationTable').DataTable().ajax.reload();
        });

        $('#addStationForm').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: window.APP_URL+'/stations/store',
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    $('#addStationModal').modal('hide');
                    alert('เพิ่มสถานีเรียบร้อยแล้ว');
                    // ตัวอย่าง: รีโหลด DataTable
                    $('#stationTable').DataTable().ajax.reload();
                },
                error: function(xhr) {
                    alert('เกิดข้อผิดพลาด: ' + xhr.responseText);
                }
            });
        });
        $('#ip-province').on('change', function() {
            const provinceId = $(this).val();
            console.log(provinceId);
            $('#ip-district').html('<option>-- โหลดอำเภอ... --</option>').prop('disabled', true);
            $('#ip-subdistrict').html('<option>-- เลือกตำบล --</option>').prop('disabled', true);

            if (provinceId) {
                $.ajax({
                    url: window.APP_URL+`/api/provinces/${provinceId}/districts`,
                    type: 'GET',
                    success: function(res) {
                        $('#ip-district').empty().append('<option value="">-- เลือกอำเภอ --</option>');
                        $.each(res, function(key, value) {
                            $('#ip-district').append(`<option value="${value.Id}">${value.NameInThai}</option>`);
                        });
                        $('#ip-district').prop('disabled', false);
                    }
                });
            }
        });

        $('#ip-district').on('change', function() {
            const districtId = $(this).val();
            $('#ip-subdistrict').html('<option>-- โหลดตำบล... --</option>').prop('disabled', true);
            console.log(districtId);
            if (districtId) {
                $.ajax({
                    url: window.APP_URL+`/api/districts/${districtId}/subdistricts`,
                    type: 'GET',
                    success: function(res) {
                        console.log(res)
                        $('#ip-subdistrict').empty().append('<option value="">-- เลือกตำบล --</option>');
                        $.each(res, function(key, value) {
                            $('#ip-subdistrict').append(`<option value="${value.Id}" data-postcode="${value.ZipCode}">${value.NameInThai}</option>`);
                        });
                        $('#ip-subdistrict').prop('disabled', false);
                    }
                });
            }
        });
        $('#ip-subdistrict').on('change', function() {
            // ดึง option ที่ถูกเลือก
            const selectedOption = $(this).find('option:selected');

            // ดึงค่า data-postcode
            const postcode = selectedOption.data('postcode');

            // แสดงผล หรือเอาไปใส่ใน input อื่น
            console.log("รหัสไปรษณีย์:", postcode);
            $('#ip-postcode').val(postcode); // กรณีคุณมี input เก็บ postcode

        });
    });
</script>
@endsection