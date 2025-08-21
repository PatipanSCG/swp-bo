@extends('layouts.admin')

@section('main-content')

<h1 class="h3 mb-4 text-gray-800">รายชื่อสถานีน้ำมัน</h1>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="row">
            <div class="col-md-6">
                <h6 class="m-0 font-weight-bold text-primary">ตารางสถานีน้ำมัน</h6>
            </div>
            <div class="col-md-6 text-right">
                <button class="btn btn-success mr-2" id="btn-station-images">
                    <i class="fas fa-building"></i> ภาพสถานี
                </button>
                <button class="btn btn-info" id="btn-downloadlabel">พิมพ์ Label</button>
            </div>
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
                        <th rowspan="2">SealNumber</th>
                        <th rowspan="2">KFactor</th>
                        <th rowspan="2">จัดการ</th>
                        <th rowspan="2">รูปภาพ</th>
                    </tr>
                    <tr>
                        <th>เริ่มต้น</th>
                        <th>สิ้นสุด</th>
                        <th>1 L</th>
                        <th>2 L</th>
                        <th>5 L</th>
                        <th>20 L</th>
                        <th>#1</th>
                        <th>#2</th>
                        <th>#3</th>
                        <th>#1</th>
                        <th>#2</th>
                        <th>#3</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Modal บันทึกผลการตรวจสอบ -->
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
                    <!-- Hidden Inputs -->
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
                        </div>
                    </div>

                    @php
                        $fields = [
                            'MMQ_1L' => 'ผลต่าง MMQ ที่ 1 ลิตร (mL)',
                            'MMQ_5L' => 'ผลต่าง MMQ ที่ 2 ลิตร (mL)',
                            'MPE_5L' => 'ผลต่าง MPE ที่ 5 ลิตร (%)',
                            'MPE_20L' => 'ผลต่าง MPE ที่ 20 ลิตร (%)',
                            'Repeat5L_1' => 'ผลต่าง (mL) ทดสอบ 5 ลิตร (ครั้งที่ 1)',
                            'Repeat5L_2' => 'ผลต่าง (mL) ทดสอบ 5 ลิตร (ครั้งที่ 2)',
                            'Repeat5L_3' => 'ผลต่าง (mL) ทดสอบ 5 ลิตร (ครั้งที่ 3)',
                            'Repeat20L_1' => 'ผลต่าง (mL) ทดสอบ 20 ลิตร (ครั้งที่ 1)',
                            'Repeat20L_2' => 'ผลต่าง (mL) ทดสอบ 20 ลิตร (ครั้งที่ 2)',
                            'Repeat20L_3' => 'ผลต่าง (mL) ทดสอบ 20 ลิตร (ครั้งที่ 3)',
                        ];
                    @endphp
                    
                    <div class="form-row">
                        @foreach($fields as $field => $label)
                        <div class="form-group col-md-6">
                            <label for="{{ $field }}">{{ $label }}</label>
                            <select name="{{ $field }}" class="form-control">
                                @for($i = -50; $i <= 50; $i += 5)
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
                    <div class="form-group">
                        <label>ค่า K</label>
                        <input type="text" class="form-control" name="KFactor">
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

<!-- Modal อัพโหลดรูปภาพสถานี -->
<div class="modal fade" id="stationImageModal" tabindex="-1" role="dialog" aria-labelledby="stationImageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-building"></i> อัพโหลดรูปภาพสถานี
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- ข้อมูลสถานี -->
                <div class="alert alert-success">
                    <div class="row">
                        <div class="col-md-6"><strong>รหัสงาน:</strong> <span id="station-work-id"></span></div>
                        <div class="col-md-6"><strong>รหัสสถานี:</strong> <span id="station-station-id"></span></div>
                    </div>
                </div>

                <!-- พื้นที่อัพโหลด -->
                <div class="upload-section border rounded p-4 text-center mb-4" style="border: 3px dashed #28a745; background-color: #f8fff8;">
                    <div class="upload-buttons mb-3">
                        <button type="button" class="btn btn-success btn-lg mr-3 mb-2" id="btn-station-camera">
                            <i class="fas fa-camera fa-lg"></i> ถ่ายภาพสถานี
                        </button>
                        <button type="button" class="btn btn-primary btn-lg mb-2" id="btn-station-gallery">
                            <i class="fas fa-images fa-lg"></i> เลือกจากแกลเลอรี่
                        </button>
                    </div>
                    <p class="text-muted mb-0"><i class="fas fa-info-circle"></i> รองรับไฟล์: JPG, PNG, GIF, WebP (สูงสุด 10MB)</p>
                </div>

                <!-- Hidden file inputs สำหรับสถานี -->
                <input type="file" id="stationCameraInput" accept="image/*" capture="camera" style="display: none;">
                <input type="file" id="stationGalleryInput" accept="image/*" style="display: none;">

                <!-- Preview สำหรับสถานี -->
                <div id="stationImagePreview" class="mt-3" style="display: none;">
                    <label class="font-weight-bold"><i class="fas fa-eye"></i> ตัวอย่างรูปภาพ:</label>
                    <div class="text-center">
                        <img id="stationPreviewImg" class="img-thumbnail shadow" style="max-width: 100%; max-height: 300px;">
                    </div>
                </div>

                <!-- รายการรูปภาพสถานีที่มีอยู่ -->
                <div id="existingStationImages" class="mt-4">
                    <h6 class="font-weight-bold"><i class="fas fa-folder-open"></i> รูปภาพสถานีที่มีอยู่:</h6>
                    <div id="stationImagesList" class="row">
                        <div class="col-12 text-center text-muted p-3">
                            <i class="fas fa-spinner fa-spin"></i> กำลังโหลด...
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn-station-upload" class="btn btn-success btn-lg" style="display: none;">
                    <i class="fas fa-upload"></i> <span id="station-upload-text">อัพโหลดรูปภาพสถานี</span>
                    <div id="station-upload-spinner" class="spinner-border spinner-border-sm ml-2" style="display: none;"></div>
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal อัพโหลดรูปภาพ -->
<div class="modal fade" id="imageUploadModal" tabindex="-1" role="dialog" aria-labelledby="imageUploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageUploadModalTitle">อัพโหลดรูปภาพ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- ข้อมูลหัวจ่าย -->
                <div class="alert alert-info">
                    <div class="row">
                        <div class="col-md-6"><strong>เลขหัวจ่าย:</strong> <span id="img-nozzle-number"></span></div>
                        <div class="col-md-6"><strong>ชนิดรูปภาพ:</strong> <span id="img-type-name">-- เลือกประเภท --</span></div>
                    </div>
                </div>

                <!-- เลือกประเภทรูปภาพ -->
                <div class="form-group">
                    <label for="imageTypeSelect"><i class="fas fa-image"></i> เลือกประเภทรูปภาพ:</label>
                    <select id="imageTypeSelect" class="form-control form-control-lg">
                        <option value="">-- เลือกประเภท --</option>
                        <option value="2">2. แผ่นหมายเลขเครื่อง</option>
                        <option value="3">3. ซีล เครื่องหมายคำรับรอง</option>
                        <option value="4">4. ทดสอบ 5 ลิตร</option>
                        <option value="5">5. ทดสอบ 20 ลิตร</option>
                        <option value="6">6. ติด QR code</option>
                        <option value="7">7. ซีล เมนเบอร์ด</option>
                        <option value="8">8. ซีล Meter</option>
                        <option value="9">9. ตู้</option>
                    </select>
                </div>

                <!-- พื้นที่อัพโหลด -->
                <div class="upload-section border rounded p-4 text-center mb-4" style="border: 3px dashed #007bff; background-color: #f8f9ff;">
                    <div class="upload-buttons mb-3">
                        <button type="button" class="btn btn-primary btn-lg mr-3 mb-2" id="btn-camera">
                            <i class="fas fa-camera fa-lg"></i> ถ่ายภาพ
                        </button>
                        <button type="button" class="btn btn-success btn-lg mb-2" id="btn-gallery">
                            <i class="fas fa-images fa-lg"></i> เลือกจากแกลเลอรี่
                        </button>
                    </div>
                    <p class="text-muted mb-0"><i class="fas fa-info-circle"></i> รองรับไฟล์: JPG, PNG, GIF, WebP (สูงสุด 10MB)</p>
                </div>

                <!-- Hidden file inputs -->
                <input type="file" id="cameraInput" accept="image/*" capture="camera" style="display: none;">
                <input type="file" id="galleryInput" accept="image/*" style="display: none;">

                <!-- Preview -->
                <div id="imagePreview" class="mt-3" style="display: none;">
                    <label class="font-weight-bold"><i class="fas fa-eye"></i> ตัวอย่างรูปภาพ:</label>
                    <div class="text-center">
                        <img id="previewImg" class="img-thumbnail shadow" style="max-width: 100%; max-height: 300px;">
                    </div>
                </div>

                <!-- รายการรูปภาพที่มีอยู่ -->
                <div id="existingImages" class="mt-4">
                    <h6 class="font-weight-bold"><i class="fas fa-folder-open"></i> รูปภาพที่มีอยู่:</h6>
                    <div id="imagesList" class="row">
                        <div class="col-12 text-center text-muted p-3">
                            <i class="fas fa-spinner fa-spin"></i> กำลังโหลด...
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn-upload" class="btn btn-primary btn-lg" style="display: none;">
                    <i class="fas fa-upload"></i> <span id="upload-text">อัพโหลด</span>
                    <div id="upload-spinner" class="spinner-border spinner-border-sm ml-2" style="display: none;"></div>
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
            </div>
        </div>
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

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style>
.image-card {
    position: relative;
    margin-bottom: 15px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.image-card:hover {
    border-color: #007bff;
    box-shadow: 0 4px 12px rgba(0,123,255,0.15);
}

.image-card img {
    width: 100%;
    height: 150px;
    object-fit: cover;
}

.image-card .badge {
    position: absolute;
    top: 8px;
    left: 8px;
    font-size: 11px;
    font-weight: 600;
    z-index: 2;
}

.image-card .btn-delete {
    position: absolute;
    top: 8px;
    right: 8px;
    width: 28px;
    height: 28px;
    padding: 0;
    border-radius: 50%;
    z-index: 2;
}

.image-card .image-actions {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(transparent, rgba(0,0,0,0.7));
    padding: 8px;
    text-align: center;
}

.image-card .image-info {
    background: rgba(255,255,255,0.95);
    padding: 8px;
    text-align: center;
    font-size: 11px;
    border-top: 1px solid #e9ecef;
}

.upload-section:hover {
    background-color: #e3f2fd !important;
    border-color: #2196f3 !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(33,150,243,0.15);
}

#imagesList .col-lg-2, #imagesList .col-md-3, #imagesList .col-sm-4 {
    padding: 8px;
}

.btn-camera, .btn-gallery {
    min-width: 160px;
    font-weight: 600;
    border-radius: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.btn-camera:hover, .btn-gallery:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.modal-xl {
    max-width: 1200px;
}

@media (max-width: 768px) {
    .upload-buttons {
        flex-direction: column;
        align-items: center;
    }
    
    .btn-camera, .btn-gallery {
        width: 200px;
        margin: 5px 0;
    }
    
    .modal-xl {
        max-width: 95%;
    }
}
</style>

<script>
$(document).ready(function() {
    const workData = @json($works);
    const WorkID = workData[0]?.WorkID ?? null;
    const stationID = workData[0]?.StationID ?? null;
    
    // ตัวแปรสำหรับเก็บข้อมูลปัจจุบัน
    let currentWorkID = null;
    let currentStationID = null;
    let currentNozzleID = null;
    let currentNozzleNumber = null;
    let selectedFile = null;
    let selectedStationFile = null;
    let currentImageType = null;

    // Image type names
    const imageTypeNames = {
        '2': 'แผ่นหมายเลขเครื่อง',
        '3': 'ซีล เครื่องหมายคำรับรอง',
        '4': 'ทดสอบ 5 ลิตร',
        '5': 'ทดสอบ 20 ลิตร',
        '6': 'ติด QR code',
        '7': 'ซีล เมนเบอร์ด',
        '8': 'ซีล Meter',
        '9': 'ตู้'
    };

    // DataTable initialization
    $('#works-table').DataTable({
        ajax: {
            url: window.APP_URL + `/api/works/${WorkID}`,
        },
        rowId: 'StationID',
        columns: [
            {
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
            { data: 'SealNumber' },
            { data: 'KFactor' },
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
            },
            {
                data: null,
                render: function(data, type, row) {
                    return `
                        <button class="btn btn-sm btn-info btn-images"
                            data-WorkID="${row.WorkID}"
                            data-NozzleID="${row.NozzleID}"
                            data-nozzlenumber="${row.NozzleNumber}">
                            <i class="fas fa-images"></i> รูปภาพ
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

    // Station images modal
    $('#btn-station-images').click(function() {
        currentWorkID = WorkID;
        currentStationID = stationID;
        
        $('#station-work-id').text(WorkID || 'N/A');
        $('#station-station-id').text(stationID || 'N/A');
        
        resetStationImageUpload();
        loadExistingStationImages();
        $('#stationImageModal').modal('show');
    });

    // Station camera button
    $('#btn-station-camera').click(function() {
        $('#stationCameraInput').click();
    });

    // Station gallery button  
    $('#btn-station-gallery').click(function() {
        $('#stationGalleryInput').click();
    });

    // Station file input change handlers
    $('#stationCameraInput, #stationGalleryInput').change(function() {
        handleStationFileSelect(this.files[0]);
    });

    // Station upload button
    $('#btn-station-upload').click(function() {
        uploadStationImage();
    });

    // Download label button
    document.getElementById('btn-downloadlabel').addEventListener('click', function() {
        window.location.href = window.APP_URL + `/api/export-nozzles/${stationID}`;
    });

    // Inspection modal
    $(document).on('click', '.btn-inspect', function() {
        const data = $(this).data();
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

        $('#inspectionModal').modal('show');
    });

    // Image upload modal
    $(document).on('click', '.btn-images', function() {
        const data = $(this).data();
        currentWorkID = data.workid;
        currentNozzleID = data.nozzleid;
        currentNozzleNumber = data.nozzlenumber;
        
        $('#img-nozzle-number').text(data.nozzlenumber);
        $('#imageTypeSelect').val('');
        $('#img-type-name').text('-- เลือกประเภท --');
        
        resetImageUpload();
        loadExistingImages();
        $('#imageUploadModal').modal('show');
    });

    // Image type selection
    $('#imageTypeSelect').change(function() {
        const typeValue = $(this).val();
        currentImageType = typeValue;
        
        if (typeValue) {
            $('#img-type-name').text(imageTypeNames[typeValue]);
            loadExistingImagesForType(typeValue);
        } else {
            $('#img-type-name').text('-- เลือกประเภท --');
            loadExistingImages();
        }
    });

    // Camera button
    $('#btn-camera').click(function() {
        if (!currentImageType) {
            alert('กรุณาเลือกประเภทรูปภาพก่อน');
            return;
        }
        $('#cameraInput').click();
    });

    // Gallery button  
    $('#btn-gallery').click(function() {
        if (!currentImageType) {
            alert('กรุณาเลือกประเภทรูปภาพก่อน');
            return;
        }
        $('#galleryInput').click();
    });

    // File input change handlers
    $('#cameraInput, #galleryInput').change(function() {
        handleFileSelect(this.files[0]);
    });

    // Upload button
    $('#btn-upload').click(function() {
        uploadImage();
    });

    // Inspection form submit
    $('#inspectionForm').submit(function(e) {
        e.preventDefault();
        const formData = $(this).serialize();

        $.post(window.APP_URL + '/api/inspection-records', formData, function(response) {
            alert('บันทึกสำเร็จ');
            $('#inspectionModal').modal('hide');
            $('#works-table').DataTable().ajax.reload();
        }).fail(function() {
            alert('เกิดข้อผิดพลาด');
        });
    });

    // Station image functions
    function handleStationFileSelect(file) {
        if (!file) return;

        // ตรวจสอบประเภทไฟล์
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            alert('กรุณาเลือกไฟล์รูปภาพที่ถูกต้อง (JPG, PNG, GIF, WebP)');
            return;
        }

        // ตรวจสอบขนาดไฟล์ (10MB)
        if (file.size > 10 * 1024 * 1024) {
            alert('ขนาดไฟล์ต้องไม่เกิน 10MB');
            return;
        }

        selectedStationFile = file;
        showStationImagePreview(file);
        $('#btn-station-upload').show();
    }

    function showStationImagePreview(file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            $('#stationPreviewImg').attr('src', e.target.result);
            $('#stationImagePreview').show();
        };
        reader.readAsDataURL(file);
    }

    function resetStationImageUpload() {
        selectedStationFile = null;
        $('#stationImagePreview').hide();
        $('#btn-station-upload').hide();
        $('#stationCameraInput, #stationGalleryInput').val('');
        $('#station-upload-spinner').hide();
        $('#station-upload-text').text('อัพโหลดรูปภาพสถานี');
    }

    async function uploadStationImage() {
        if (!selectedStationFile) {
            alert('กรุณาเลือกรูปภาพก่อน');
            return;
        }

        if (!currentWorkID || !currentStationID) {
            alert('ไม่พบข้อมูลงานหรือสถานี');
            return;
        }

        const formData = new FormData();
        formData.append('workid', currentWorkID);
        formData.append('type', 1); // Type 1 = ภาพสถานี
        formData.append('nozzle_id', 0); // ใช้ 0 สำหรับภาพสถานี (ไม่เกี่ยวกับ nozzle)
        formData.append('image', selectedStationFile);

        // แสดง loading
        $('#btn-station-upload').prop('disabled', true);
        $('#station-upload-spinner').show();
        $('#station-upload-text').text('กำลังอัพโหลด...');

        try {
            const response = await fetch(window.APP_URL + '/api/work-images/upload', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (result.success) {
                alert('อัพโหลดรูปภาพสถานีสำเร็จ!');
                resetStationImageUpload();
                loadExistingStationImages();
            } else {
                throw new Error(result.message || 'เกิดข้อผิดพลาดในการอัพโหลด');
            }

        } catch (error) {
            console.error('Station image upload error:', error);
            alert(`เกิดข้อผิดพลาด: ${error.message}`);
        } finally {
            $('#btn-station-upload').prop('disabled', false);
            $('#station-upload-spinner').hide();
            $('#station-upload-text').text('อัพโหลดรูปภาพสถานี');
        }
    }

    async function loadExistingStationImages() {
        if (!currentWorkID) return;

        $('#stationImagesList').html('<div class="col-12 text-center text-muted p-3"><i class="fas fa-spinner fa-spin"></i> กำลังโหลด...</div>');

        try {
            const response = await fetch(
                `${window.APP_URL}/api/work-images?workid=${currentWorkID}&type=1`,
                {
                    headers: { 'Accept': 'application/json' }
                }
            );

            const result = await response.json();

            if (result.success) {
                displayStationImages(result.data);
            } else {
                $('#stationImagesList').html('<div class="col-12 text-center text-muted p-3">ไม่มีรูปภาพสถานี</div>');
            }
        } catch (error) {
            console.error('Error loading station images:', error);
            $('#stationImagesList').html('<div class="col-12 text-center text-muted p-3">เกิดข้อผิดพลาดในการโหลด</div>');
        }
    }

    function displayStationImages(images) {
        const imagesList = $('#stationImagesList');
        imagesList.empty();

        if (images.length === 0) {
            imagesList.html('<div class="col-12 text-center text-muted p-3"><i class="fas fa-building"></i><br>ไม่มีรูปภาพสถานี</div>');
            return;
        }

        images.forEach(function(image) {
            const uploadDate = new Date(image.created_at).toLocaleDateString('th-TH', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            const imageCard = `
                <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                    <div class="image-card">
                        <img src="${image.image_url}" alt="รูปภาพสถานี" class="img-fluid" 
                             onclick="viewFullImage('${image.image_url}', 'รูปภาพสถานี', ${image.id})"
                             style="cursor: pointer;">
                        <span class="badge badge-success">สถานี</span>
                        <button type="button" class="btn btn-danger btn-sm btn-delete" 
                                onclick="deleteImage(${image.id})" title="ลบรูปภาพ">
                            <i class="fas fa-times"></i>
                        </button>
                        <div class="image-info">
                            <div class="font-weight-bold" style="font-size: 10px;">รูปภาพสถานี</div>
                            <div class="text-muted" style="font-size: 9px;">${uploadDate}</div>
                        </div>
                        <div class="image-actions">
                            <button type="button" class="btn btn-sm btn-light btn-sm mr-1" 
                                    onclick="downloadImage(${image.id}, '${image.imagename}')" title="ดาวน์โหลด">
                                <i class="fas fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-light btn-sm" 
                                    onclick="viewFullImage('${image.image_url}', 'รูปภาพสถานี', ${image.id})" title="ดูขนาดเต็ม">
                                <i class="fas fa-search-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            imagesList.append(imageCard);
        });
    }

    // Functions
    function handleFileSelect(file) {
        if (!file) return;

        // ตรวจสอบประเภทไฟล์
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            alert('กรุณาเลือกไฟล์รูปภาพที่ถูกต้อง (JPG, PNG, GIF, WebP)');
            return;
        }

        // ตรวจสอบขนาดไฟล์ (10MB)
        if (file.size > 10 * 1024 * 1024) {
            alert('ขนาดไฟล์ต้องไม่เกิน 10MB');
            return;
        }

        selectedFile = file;
        showImagePreview(file);
        $('#btn-upload').show();
    }

    function showImagePreview(file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            $('#previewImg').attr('src', e.target.result);
            $('#imagePreview').show();
        };
        reader.readAsDataURL(file);
    }

    function resetImageUpload() {
        selectedFile = null;
        selectedStationFile = null;
        currentImageType = null;
        $('#imagePreview').hide();
        $('#btn-upload').hide();
        $('#cameraInput, #galleryInput').val('');
        $('#upload-spinner').hide();
        $('#upload-text').text('อัพโหลด');
    }

    async function uploadImage() {
        if (!selectedFile || !currentImageType) {
            alert('กรุณาเลือกรูปภาพและประเภทรูปภาพ');
            return;
        }

        const formData = new FormData();
        formData.append('workid', currentWorkID);
        formData.append('type', currentImageType);
        formData.append('nozzle_id', currentNozzleID);
        formData.append('image', selectedFile);

        // แสดง loading
        $('#btn-upload').prop('disabled', true);
        $('#upload-spinner').show();
        $('#upload-text').text('กำลังอัพโหลด...');

        try {
            const response = await fetch(window.APP_URL + '/api/work-images/upload', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (result.success) {
                alert('อัพโหลดสำเร็จ!');
                resetImageUpload();
                
                // โหลดรูปภาพใหม่
                if (currentImageType) {
                    loadExistingImagesForType(currentImageType);
                } else {
                    loadExistingImages();
                }
                
                // โหลดรูปภาพสถานีใหม่ (ถ้าอยู่ใน station modal)
                if ($('#stationImageModal').hasClass('show')) {
                    loadExistingStationImages();
                }
            } else {
                throw new Error(result.message || 'เกิดข้อผิดพลาดในการอัพโหลด');
            }

        } catch (error) {
            console.error('Upload error:', error);
            alert(`เกิดข้อผิดพลาด: ${error.message}`);
        } finally {
            $('#btn-upload').prop('disabled', false);
            $('#upload-spinner').hide();
            $('#upload-text').text('อัพโหลด');
        }
    }

    async function loadExistingImages() {
        if (!currentWorkID || !currentNozzleID) return;

        $('#imagesList').html('<div class="col-12 text-center text-muted p-3"><i class="fas fa-spinner fa-spin"></i> กำลังโหลด...</div>');

        try {
            const response = await fetch(
                `${window.APP_URL}/api/work-images?workid=${currentWorkID}&nozzle_id=${currentNozzleID}`,
                {
                    headers: { 'Accept': 'application/json' }
                }
            );

            const result = await response.json();

            if (result.success) {
                displayImages(result.data);
            } else {
                $('#imagesList').html('<div class="col-12 text-center text-muted p-3">ไม่สามารถโหลดรูปภาพได้</div>');
            }
        } catch (error) {
            console.error('Error loading images:', error);
            $('#imagesList').html('<div class="col-12 text-center text-muted p-3">เกิดข้อผิดพลาดในการโหลด</div>');
        }
    }

    async function loadExistingImagesForType(type) {
        if (!currentWorkID || !currentNozzleID || !type) return;

        $('#imagesList').html('<div class="col-12 text-center text-muted p-3"><i class="fas fa-spinner fa-spin"></i> กำลังโหลด...</div>');

        try {
            const response = await fetch(
                `${window.APP_URL}/api/work-images?workid=${currentWorkID}&nozzle_id=${currentNozzleID}&type=${type}`,
                {
                    headers: { 'Accept': 'application/json' }
                }
            );

            const result = await response.json();

            if (result.success) {
                displayImages(result.data);
            } else {
                $('#imagesList').html('<div class="col-12 text-center text-muted p-3">ไม่มีรูปภาพประเภทนี้</div>');
            }
        } catch (error) {
            console.error('Error loading images for type:', error);
            $('#imagesList').html('<div class="col-12 text-center text-muted p-3">เกิดข้อผิดพลาดในการโหลด</div>');
        }
    }

    function displayImages(images) {
        const imagesList = $('#imagesList');
        imagesList.empty();

        if (images.length === 0) {
            imagesList.html('<div class="col-12 text-center text-muted p-3"><i class="fas fa-folder-open"></i><br>ไม่มีรูปภาพ</div>');
            return;
        }

        images.forEach(function(image) {
            const typeName = imageTypeNames[image.type] || `Type ${image.type}`;
            const uploadDate = new Date(image.created_at).toLocaleDateString('th-TH', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            const imageCard = `
                <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                    <div class="image-card">
                        <img src="${image.image_url}" alt="${typeName}" class="img-fluid" 
                             onclick="viewFullImage('${image.image_url}', '${typeName}', ${image.id})"
                             style="cursor: pointer;">
                        <span class="badge badge-primary">${image.type}</span>
                        <button type="button" class="btn btn-danger btn-sm btn-delete" 
                                onclick="deleteImage(${image.id})" title="ลบรูปภาพ">
                            <i class="fas fa-times"></i>
                        </button>
                        <div class="image-info">
                            <div class="font-weight-bold" style="font-size: 10px;">${typeName}</div>
                            <div class="text-muted" style="font-size: 9px;">${uploadDate}</div>
                        </div>
                        <div class="image-actions">
                            <button type="button" class="btn btn-sm btn-light btn-sm mr-1" 
                                    onclick="downloadImage(${image.id}, '${image.imagename}')" title="ดาวน์โหลด">
                                <i class="fas fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-light btn-sm" 
                                    onclick="viewFullImage('${image.image_url}', '${typeName}', ${image.id})" title="ดูขนาดเต็m">
                                <i class="fas fa-search-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            imagesList.append(imageCard);
        });
    }

    async function deleteImage(imageId) {
        if (!confirm('คุณต้องการลบรูปภาพนี้หรือไม่?')) {
            return;
        }

        try {
            const response = await fetch(`${window.APP_URL}/api/work-images/${imageId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (result.success) {
                alert('ลบรูปภาพสำเร็จ');
                
                // โหลดรูปภาพใหม่
                if (currentImageType) {
                    loadExistingImagesForType(currentImageType);
                } else {
                    loadExistingImages();
                }
            } else {
                throw new Error(result.message || 'เกิดข้อผิดพลาดในการลบ');
            }

        } catch (error) {
            console.error('Delete error:', error);
            alert(`เกิดข้อผิดพลาด: ${error.message}`);
        }
    }

    function downloadImage(imageId, filename) {
        const downloadUrl = `${window.APP_URL}/api/work-images/${imageId}/download`;
        const link = document.createElement('a');
        link.href = downloadUrl;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function viewFullImage(imageUrl, typeName, imageId) {
        const modal = `
            <div class="modal fade" id="imageViewModal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-image"></i> ${typeName}
                                <small class="text-muted">(ID: ${imageId})</small>
                            </h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body text-center" style="background-color: #f8f9fa;">
                            <img src="${imageUrl}" class="img-fluid" style="max-height: 70vh; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" onclick="downloadImage(${imageId}, 'image_${imageId}')">
                                <i class="fas fa-download"></i> ดาวน์โหลด
                            </button>
                            <button type="button" class="btn btn-danger" onclick="deleteImage(${imageId}); $('#imageViewModal').modal('hide');">
                                <i class="fas fa-trash"></i> ลบรูปภาพ
                            </button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // ลบ modal เก่า (ถ้ามี)
        $('#imageViewModal').remove();
        
        // เพิ่ม modal ใหม่
        $('body').append(modal);
        $('#imageViewModal').modal('show');
        
        // ลบ modal หลังปิด
        $('#imageViewModal').on('hidden.bs.modal', function() {
            $(this).remove();
        });
    }

    // Global functions for onclick handlers
    window.deleteImage = deleteImage;
    window.downloadImage = downloadImage;
    window.viewFullImage = viewFullImage;
});
</script>
@endsection