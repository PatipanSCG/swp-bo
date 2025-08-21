@extends('layouts.admin')

@section('main-content')

<h1 class="h3 mb-4 text-gray-800">รายชื่อสถานีน้ำมัน</h1>

<div class="card shadow mb-4">
    <div class="card-header py-3">

        <div class="row">
            <div class="col-md-6">
                <h6 class="m-0 font-weight-bold text-primary">ตารางสถานีน้ำมัน</h6>
            </div>
            <div class="col-md-6 text-right"><button class="btn btn-info" id="btn-downloadlabel">พิมพ์ Label</button></div>
        </div>
    </div>
    <div class="card-body">
        
        <div class="table-responsive">
            <table class="table table-bordered" id="works-table" width="100%" cellspacing="0">
                <thead>
        <tr>
            <th rowspan="2">ลำดับที่</th>
            <th rowspan="2">เลขลำดับประจำเครื่อง</th>
            <th rowspan="2">ชนิดน้ำมัน</th>
            <th rowspan="2">ยี่ห้อ</th>
            <th rowspan="2">MODEL</th>
            <th rowspan="2">MMQ (L)</th>
            <th rowspan="2">Q max (L/min)</th>
            <th rowspan="2">Q min (L/min)</th>
            <th rowspan="2">S/N</th>
            <th colspan="2" rowspan="1">เลขมิเตอร์</th>
            <th colspan="2" rowspan="1">ผลต่าง MMQ (mL)</th>
            <th colspan="2" rowspan="1">ผลต่าง MPE (mL)</th>
            <th colspan="3" rowspan="1">ผลต่าง Repeat (mL)<br>ทดสอบ 5L</th>
            <th colspan="3" rowspan="1">ผลต่าง Repeat (mL)<br>ทดสอบ 20L</th>
            <th rowspan="2" >จัดการ</th>
        </tr>
        <tr>
            <th >เริ่มต้น</th>
            <th >สิ้นสุด</th>
            <th >1 L</th>
            <th >2 L</th>
            <th >5 L</th>
            <th >20 L</th>
            <th >#1</th>
            <th >#2</th>
            <th >#3</th>
            <th >#1</th>
            <th >#2</th>
            <th >#3</th>
         
        </tr>
    </thead>
            </table>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="inspectionModal" tabindex="-1" role="dialog" aria-labelledby="inspectionModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <form id="inspectionForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">บันทึกผลการตรวจสอบหัวจ่าย</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <!-- ✅ Hidden Inputs -->
                <input type="hidden" name="WorkID" id="input-WorkID">
                <input type="hidden" name="StationID" id="input-StationID">
                <input type="hidden" name="DispenserID" id="input-DispenserID">
                <input type="hidden" name="NozzleID" id="input-NozzleID">
                <input type="hidden" name="NozzleNumber" id="input-NozzleNumber">
            <div class="row">
                <div class="col-md-6"><strong>เลขหัวจ่าย:</strong> <span id="nozzle-number"></span></div>
                <div class="col-md-6"><strong>ชนิดน้ำมัน:</strong> <span id="fuel-type"></span></div>
                <div class="col-md-6"><strong>ยี่ห้อ:</strong> <span id="brand-name"></span></div>
                <div class="col-md-6"><strong>โมเดล:</strong> <span id="model-name"></span></div>
                <div class="col-md-6"><strong>S/N:</strong> <span id="serial-number"></span></div>
            </div>

            <hr>
            <div class="row">
            <div class="form-group col-md-6">
                <label>เลขมิเตอร์ เริ่มต้น</label>
                <input type="number" class="form-control" name="MitterBegin" required>
            </div>
            <div class="form-group col-md-6">
                <label>เลขมิเตอร์ สิ้นสุด</label>
                <input type="number" class="form-control" name="MitterEnd" required>
            </div></div>
@php
    $fields = [
        'MMQ_1L' => 'ผลต่าง MMQ ที่ 1 ลิตร (mL)',
        'MMQ_5L' => 'ผลต่าง MMQ ที่ 2 ลิตร (mL)',
        'MPE_5L' => 'ผลต่าง MPE ที่ 5 ลิตร (%)',
        'MPE_20L' => 'ผลต่าง MPE ที่ 20 ลิตร (%)',
        'Repeat5L_1' => 'ผลต่าง (mL) ทดสอบ  5 ลิตร (ครั้งที่ 1)',
        'Repeat5L_2' => 'ผลต่าง (mL) ทดสอบ  5 ลิตร (ครั้งที่ 2)',
        'Repeat5L_3' => 'ผลต่าง (mL) ทดสอบ  5 ลิตร (ครั้งที่ 3)',
        'Repeat20L_1' => 'ผลต่าง (mL) ทดสอบ  20 ลิตร (ครั้งที่ 1)',
        'Repeat20L_2' => 'ผลต่าง (mL) ทดสอบ  20 ลิตร (ครั้งที่ 2)',
        'Repeat20L_3' => 'ผลต่าง (mL) ทดสอบ  20 ลิตร (ครั้งที่ 3)',
    ];
@endphp
            <div class="form-row">
                @foreach($fields as $field => $label)
                <div class="form-group col-md-6">
                    <label for="{{ $field }}">{{ $label }}</label>
                    <select name="{{ $field }}" class="form-control">
                         @for($i = -20; $i <= 20; $i += 5)
                            <option value="{{ $i }}" {{ $i === 0 ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                @endforeach
            </div>

            <div class="form-group">
                <label>เลขซีล</label>
                <input type="text" class="form-control" name="SealNumber">
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">บันทึก</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
        </div>
      </div>
    </form>
  </div>
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
    $(document).ready(function() {
        const workData = @json($works);
        const WorkID = workData[0]?.WorkID ?? null; // ใช้งานแรก
        const stationID = workData[0]?.StationID ?? null; // ใช้งานแรก

        $('#works-table').DataTable({
           ajax: {
                url: `{{env('APP_URL')}}api/works/${WorkID}`,
                // data: function(d) {
                //     d.name = $('#filter-name').val();
                //     d.taxid = $('#filter-taxid').val();
                //     d.province = $('#filter-province').val();
                //     d.brand = $('#filter-brand').val();
                // }
            },
            rowId: 'StationID',
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                { data: 'NozzleNumber' },
                { data: 'fuletype' },
                { data: 'Brand' },
                { data: 'Modal' },
                { data: 'MMQ' },
                { data: 'Qmax' },
                { data: 'Qmin' },
                { data: 'SN' },
                 { data: 'MitterBegin' },
                 { data: 'MitterEnd' },
                 { data: 'MMQ_1L' },
                 { data: 'MMQ_5L' },
                 { data: 'MPE_5L' },
                 { data: 'MPE_20L' },
                 { data: 'Repeat5L_1' },
                 { data: 'Repeat5L_2' },
                 { data: 'Repeat5L_3' },
                 { data: 'Repeat20L_1' },
                 { data: 'Repeat20L_2' },
                 { data: 'Repeat20L_3' },
                {
                    data: null,
                    render: function(data, type, row) {
                        if (row.Status == 1) {
                            return `
                            <button class="btn btn-sm btn-warning btn-inspect"
                                data-WorkID="${row.WorkID}"
                                data-StationID="${row.StationID}"
                                data-DispenserID="${row.DispenserID}"
                                data-nozzlenumber="${row.NozzleNumber}"
                                data-NozzleID="${row.NozzleID}"
                                data-fueltype="${row.fuletype}"
                                data-brandname="${row.Brand}"
                                data-model="${row.Modal}"
                                data-serial="${row.SN}">
                                แก้ไข
                            </button>`;
                        }

                        return `
                            <button class="btn btn-sm btn-primary btn-inspect"
                                data-WorkID="${row.WorkID}"
                                data-StationID="${row.StationID}"
                                data-DispenserID="${row.DispenserID}"
                                data-nozzlenumber="${row.NozzleNumber}"
                                data-NozzleID="${row.NozzleID}"
                                data-fueltype="${row.fuletype}"
                                data-brandname="${row.Brand}"
                                data-model="${row.Modal}"
                                data-serial="${row.SN}">
                                เพิ่มข้อมูล
                            </button>`;
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
        document.getElementById('btn-downloadlabel').addEventListener('click', function () {
        window.location.href = `{{env('APP_URL')}}api/export-nozzles/${stationID}`;
    });
    });
     
    $(document).on('click', '.btn-inspect', function() {
    const data = $(this).data();
console.log(data.workid);
$('#inspectionForm')[0].reset();
    $('#nozzle-number').text(data.nozzlenumber);
    $('#fuel-type').text(data.fueltype);
    $('#brand-name').text(data.brandname);
    $('#model-name').text(data.model);
    $('#serial-number').text(data.serial);

    $('#input-WorkID').val(data.workid);
    $('#input-StationID').val(data.stationid);
    $('#input-NozzleNumber').val(data.nozzlenumber);
    $('#input-DispenserID').val(data.dispenserid);
    $('#input-NozzleID').val(data.nozzleid);
console.log($('#inspectionForm').serializeArray());

    // หากต้องการ reset ฟอร์ม:

    // เปิด modal
    $('#inspectionModal').modal('show');
});
$('#inspectionForm').submit(function(e) {
    e.preventDefault();
    const formData = $(this).serialize();

    $.post('{{env('APP_URL')}}api/inspection-records', formData, function(response) {
        alert('บันทึกสำเร็จ');
        $('#inspectionModal').modal('hide');
        $('#works-table').DataTable().ajax.reload();
    }).fail(function() {
        alert('เกิดข้อผิดพลาด');
    });
});
</script>
@endsection