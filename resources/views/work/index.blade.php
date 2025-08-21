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

<!-- Modal อัพโหลดรูปภาพสถานี -->
<div class="modal fade" id="stationImageModal" tabindex="-1" role="dialog" aria-labelledby="stationImageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-building"></i> จัดการรูปภาพสถานี
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- ข้อมูลสถานี -->
                <div class="alert alert-success">
                    <div class="row">
                        <div class="col-md-4"><strong>รหัสงาน:</strong> <span id="station-work-id"></span></div>
                        <div class="col-md-4"><strong>รหัสสถานี:</strong> <span id="station-station-id"></span></div>
                        <div class="col-md-4"><strong>ชื่อสถานี:</strong> <span id="station-name"></span></div>
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
    border-color: #28a745;
    box-shadow: 0 4px 12px rgba(40,167,69,0.15);
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
    background-color: #e8f5e8 !important;
    border-color: #20c997 !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(32,201,151,0.15);
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
$(document).ready(function () {
    // ตัวแปรสำหรับจัดการรูปภาพสถานี
    let currentWorkID = null;
    let currentStationID = null;
    let currentStationName = null;
    let selectedStationFile = null;

    // DataTable initialization
    $('#works-table').DataTable({
        ajax: {
            url: window.APP_URL + "/api/works",
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
                    return `
                        <div class="btn-group" role="group">
                            <a href="${window.APP_URL}/stations/${row.StationID}/detail" class="btn btn-success btn-sm">
                                <i class="fas fa-info-circle"></i> ข้อมูลสถานี
                            </a>
                            <a href="${window.APP_URL}/stations/${row.StationID}/CheckDispenser" class="btn btn-info btn-sm">
                                <i class="fas fa-clipboard-check"></i> ตรวจตู้
                            </a>
                            <a href="${window.APP_URL}/works/${row.WorkID}/detail" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> บันทึกการตรวจ
                            </a>
                            <button class="btn btn-secondary btn-sm btn-station-images" 
                                    data-work-id="${row.WorkID}" 
                                    data-station-id="${row.StationID}" 
                                    data-station-name="${row.station.StationName}">
                                <i class="fas fa-images"></i> ภาพสถานี
                            </button>
                        </div>
                    `;
                }
            }
        ],
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/th.json"
        },
        columnDefs: [
            { targets: 7, orderable: false } // ปิดการเรียงลำดับคอลัมน์จัดการ
        ]
    });

    // Station images modal handler
    $(document).on('click', '.btn-station-images', function() {
        currentWorkID = $(this).data('work-id');
        currentStationID = $(this).data('station-id');
        currentStationName = $(this).data('station-name');
        
        $('#station-work-id').text(currentWorkID || 'N/A');
        $('#station-station-id').text(currentStationID || 'N/A');
        $('#station-name').text(currentStationName || 'N/A');
        
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
        formData.append('nozzle_id', 0); // ใช้ 0 สำหรับภาพสถานี
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
                                onclick="deleteStationImage(${image.id})" title="ลบรูปภาพ">
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

    async function deleteStationImage(imageId) {
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
                loadExistingStationImages();
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
                            <button type="button" class="btn btn-primary" onclick="downloadImage(${imageId}, 'station_image_${imageId}')">
                                <i class="fas fa-download"></i> ดาวน์โหลด
                            </button>
                            <button type="button" class="btn btn-danger" onclick="deleteStationImage(${imageId}); $('#imageViewModal').modal('hide');">
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
    window.deleteStationImage = deleteStationImage;
    window.downloadImage = downloadImage;
    window.viewFullImage = viewFullImage;
});
</script>
@endsection