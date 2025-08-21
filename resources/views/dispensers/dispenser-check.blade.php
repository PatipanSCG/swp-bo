@extends('layouts.admin')

@section('main-content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-clipboard-check text-primary"></i> 
            ตรวจเช็คตู้จ่ายน้ำมัน
        </h1>
        <div class="btn-group">
            <button class="btn btn-success btn-sm" id="btn-export-pdf" style="display: none;">
                <i class="fas fa-file-pdf"></i> Export รายงาน PDF
            </button>
            <a href="{{ url()->previous() }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> กลับ
            </a>
        </div>
    </div>

    <!-- Station Info Card -->
    <div class="card shadow-sm mb-4 border-left-primary">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary font-weight-bold mb-1">ข้อมูลสถานี</h6>
                            <div class="mb-2">
                                <small class="text-muted">รหัสงาน:</small>
                                <span class="font-weight-bold ml-2" id="work-id">{{ $workId ?? 'N/A' }}</span>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">รหัสสถานี:</small>
                                <span class="font-weight-bold ml-2" id="station-id">{{ $stationId ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <small class="text-muted">ชื่อสถานี:</small>
                                <span class="font-weight-bold ml-2" id="station-name">{{ $stationName ?? 'N/A' }}</span>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">วันที่ตรวจ:</small>
                                <span class="font-weight-bold ml-2" id="check-date">{{ now()->format('d/m/Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 text-lg-right">
                    <div class="text-center">
                        <div class="text-muted small mb-1">ความคืบหน้าโดยรวม</div>
                        <div class="progress mb-2" style="height: 25px;">
                            <div class="progress-bar bg-gradient-success" id="overall-progress" style="width: 0%">
                                <span id="overall-progress-text">0%</span>
                            </div>
                        </div>
                        <div class="small text-muted">
                            <span id="completed-dispensers">0</span> / <span id="total-dispensers">0</span> ตู้
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dispensers Grid -->
    <div class="row" id="dispensers-container">
        <!-- Loading State -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">กำลังโหลด...</span>
                    </div>
                    <div class="mt-3 text-muted">กำลังโหลดข้อมูลตู้จ่าย...</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal เริ่มการตรวจเช็ค -->
<div class="modal fade" id="startCheckModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-play-circle"></i> เริ่มการตรวจเช็คตู้จ่าย
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="startCheckForm">
                <div class="modal-body">
                    <input type="hidden" id="selected-dispenser-id">
                    
                    <!-- Dispenser Info -->
                    <div class="alert alert-info border-left-info">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>ตู้จ่าย:</strong> <span id="selected-dispenser-number"></span>
                            </div>
                            <div class="col-md-6">
                                <strong>ยี่ห้อ:</strong> <span id="selected-dispenser-brand"></span>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <strong>รุ่น:</strong> <span id="selected-dispenser-model"></span>
                            </div>
                            <div class="col-md-6">
                                <strong>S/N:</strong> <span id="selected-dispenser-serial"></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Inspector Info -->
                    <div class="form-group">
                        <label for="inspector-name" class="font-weight-bold">
                            <i class="fas fa-user"></i> ชื่อผู้ตรวจ *
                        </label>
                        <input type="text" class="form-control form-control-lg" id="inspector-name" 
                               placeholder="กรอกชื่อผู้ตรวจเช็ค" value="System">
                        <small class="form-text text-muted">กรุณากรอกชื่อผู้ทำการตรวจเช็คตู้จ่ายนี้</small>
                    </div>

                    <!-- Check Items Preview -->
                    <div class="card bg-light">
                        <div class="card-header py-2">
                            <h6 class="mb-0 text-dark">
                                <i class="fas fa-list-check"></i> รายการที่ต้องตรวจเช็ค (10 ข้อ)
                            </h6>
                        </div>
                        <div class="card-body py-3">
                            <div class="row text-center">
                                <div class="col-md-3">
                                    <div class="text-primary font-weight-bold">1-3</div>
                                    <small class="text-muted">ระบบสายพาน<br>และหัวจ่าย</small>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-primary font-weight-bold">4-6</div>
                                    <small class="text-muted">ระบบท่อจ่าย<br>และจอแสดงผล</small>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-primary font-weight-bold">7-8</div>
                                    <small class="text-muted">ระบบความปลอดภัย<br>ใต้ตู้</small>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-primary font-weight-bold">9-10</div>
                                    <small class="text-muted">ระบบกราวด์<br>และฐานตู้</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-play"></i> เริ่มตรวจเช็ค
                    </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal การตรวจเช็ค -->
<div class="modal fade" id="checkModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-clipboard-check"></i> 
                    การตรวจเช็คตู้จ่าย - <span id="check-dispenser-info"></span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Progress Section -->
                <div class="card bg-light mb-4">
                    <div class="card-body py-3">
                        <div class="row align-items-center">
                            <div class="col-lg-8">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="font-weight-bold text-dark">ความคืบหน้าการตรวจเช็ค</span>
                                    <span id="progress-text" class="text-primary font-weight-bold">0/10 (0%)</span>
                                </div>
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar bg-gradient-success" id="progress-bar" 
                                         style="width: 0%" role="progressbar">
                                        <span class="progress-text"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 text-lg-right mt-3 mt-lg-0">
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="text-success font-weight-bold" id="normal-count">0</div>
                                        <small class="text-muted">ปกติ</small>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-danger font-weight-bold" id="problem-count">0</div>
                                        <small class="text-muted">ปัญหา</small>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-secondary font-weight-bold" id="remaining-count">10</div>
                                        <small class="text-muted">เหลือ</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Check Items Container -->
                <div id="check-items-container" class="mb-4">
                    <!-- รายการตรวจเช็คจะถูกโหลดที่นี่ -->
                </div>

                <!-- Notes Section -->
                <div class="card border-secondary">
                    <div class="card-header bg-secondary text-white py-2">
                        <h6 class="mb-0">
                            <i class="fas fa-sticky-note"></i> หมายเหตุการตรวจเช็ค
                        </h6>
                    </div>
                    <div class="card-body">
                        <textarea class="form-control" id="check-remarks" rows="3" 
                                  placeholder="หมายเหตุเพิ่มเติมสำหรับการตรวจเช็คครั้งนี้ (ถ้ามี)"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn-complete-check" class="btn btn-success btn-lg" style="display: none;">
                    <i class="fas fa-check-circle"></i> เสร็จสิ้นการตรวจเช็ค
                </button>
                <button type="button" class="btn btn-info" id="btn-save-progress">
                    <i class="fas fa-save"></i> บันทึกความคืบหน้า
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal แนบรูปภาพ -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fas fa-camera"></i> แนบรูปภาพปัญหา
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="current-detail-id">
                
                <!-- Item Info -->
                <div class="alert alert-warning">
                    <strong>รายการที่ตรวจ:</strong> <span id="image-item-title"></span>
                </div>

                <!-- Upload Area -->
                <div class="card border-warning mb-4">
                    <div class="card-body text-center">
                        <div class="upload-zone p-4 border border-dashed border-warning rounded bg-light">
                            <i class="fas fa-cloud-upload-alt fa-3x text-warning mb-3"></i>
                            <h5 class="text-dark">อัพโหลดรูปภาพปัญหา</h5>
                            <p class="text-muted mb-4">คลิกปุ่มด้านล่างเพื่อถ่ายภาพหรือเลือกจากแกลเลอรี่</p>
                            
                            <div class="btn-group btn-group-lg">
                                <button type="button" class="btn btn-warning" id="btn-camera">
                                    <i class="fas fa-camera"></i> ถ่ายภาพ
                                </button>
                                <button type="button" class="btn btn-info" id="btn-gallery">
                                    <i class="fas fa-images"></i> แกลเลอรี่
                                </button>
                            </div>
                            
                            <div class="mt-3">
                                <small class="text-muted">
                                    รองรับ: JPG, PNG, GIF, WebP | ขนาดสูงสุด: 10MB
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- File Inputs -->
                <input type="file" id="camera-input" accept="image/*" capture="camera" style="display: none;">
                <input type="file" id="gallery-input" accept="image/*" style="display: none;">

                <!-- Preview Section -->
                <div id="image-preview" class="card border-success mb-4" style="display: none;">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-eye"></i> ตัวอย่างรูปภาพ
                        </h6>
                    </div>
                    <div class="card-body text-center">
                        <img id="preview-image" class="img-fluid rounded shadow" style="max-height: 300px;">
                        
                        <div class="form-group mt-3">
                            <label for="image-description" class="font-weight-bold">คำอธิบายรูปภาพ:</label>
                            <textarea class="form-control" id="image-description" rows="3" 
                                      placeholder="อธิบายปัญหาที่พบในรูปภาพนี้"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Existing Images -->
                <div class="card border-info">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-folder-open"></i> รูปภาพที่แนบแล้ว
                        </h6>
                    </div>
                    <div class="card-body">
                        <div id="existing-images" class="row">
                            <!-- รูปภาพจะแสดงที่นี่ -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn-upload" class="btn btn-warning btn-lg" style="display: none;">
                    <i class="fas fa-upload"></i> 
                    <span id="upload-text">อัพโหลดรูปภาพ</span>
                    <div id="upload-spinner" class="spinner-border spinner-border-sm ml-2" style="display: none;"></div>
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<style>
/* Dispenser Cards */
.dispenser-card {
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.dispenser-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.dispenser-card.completed {
    border-color: #28a745;
    background: linear-gradient(135deg, #f8fff8 0%, #e8f5e8 100%);
}

.dispenser-card.in-progress {
    border-color: #ffc107;
    background: linear-gradient(135deg, #fffbf0 0%, #fff3cd 100%);
}

.dispenser-card.pending {
    border-color: #6c757d;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

/* Check Items */
.check-item {
    border: 2px solid #e3e6f0;
    border-radius: 12px;
    margin-bottom: 25px;
    background: white;
    transition: all 0.3s ease;
    overflow: hidden;
}

.check-item.completed {
    border-color: #28a745;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2);
}

.check-item.problem {
    border-color: #dc3545;
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.2);
}

.check-item-header {
    background: linear-gradient(135deg, #4e73df 0%, #6c5ce7 100%);
    color: white;
    padding: 20px;
    position: relative;
}

.check-item-header::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0.1) 100%);
}

.check-item-body {
    padding: 25px;
}

.item-number {
    background: rgba(255, 255, 255, 0.2);
    padding: 5px 12px;
    border-radius: 20px;
    font-weight: bold;
    margin-right: 15px;
    display: inline-block;
}

.result-buttons {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-bottom: 20px;
}

.btn-result {
    padding: 15px 20px;
    font-weight: 600;
    border: 3px solid transparent;
    border-radius: 10px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.btn-result::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.5s;
}

.btn-result:hover::before {
    left: 100%;
}

.btn-result.active {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
}

.btn-normal {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border-color: #28a745;
    color: white;
}

.btn-normal:hover, .btn-normal.active {
    background: linear-gradient(135deg, #218838 0%, #1ea085 100%);
    border-color: #1e7e34;
    color: white;
}

.btn-problem {
    background: linear-gradient(135deg, #dc3545 0%, #e74c3c 100%);
    border-color: #dc3545;
    color: white;
}

.btn-problem:hover, .btn-problem.active {
    background: linear-gradient(135deg, #c82333 0%, #c0392b 100%);
    border-color: #bd2130;
    color: white;
}

/* Problem Section */
.problem-section {
    background: linear-gradient(135deg, #fff5f5 0%, #fed7d7 100%);
    border: 2px solid #feb2b2;
    border-radius: 12px;
    padding: 20px;
    margin-top: 20px;
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Status Badges */
.status-badge {
    padding: 8px 16px;
    border-radius: 25px;
    font-weight: 600;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.status-completed {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
}

.status-problem {
    background: linear-gradient(135deg, #dc3545 0%, #e74c3c 100%);
    color: white;
}

.status-pending {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    color: white;
}

.status-progress {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
    color: white;
}

/* Progress Bars */
.progress {
    border-radius: 50px;
    background: #e9ecef;
    overflow: hidden;
}

.progress-bar {
    border-radius: 50px;
    transition: width 0.5s ease;
    background: linear-gradient(90deg, #28a745 0%, #20c997 50%, #17a2b8 100%);
    position: relative;
}

.progress-bar::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

/* Image Thumbnails */
.image-thumbnail {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 8px;
    cursor: pointer;
    margin: 5px;
    border: 3px solid #e9ecef;
    transition: all 0.3s ease;
}

.image-thumbnail:hover {
    border-color: #4e73df;
    transform: scale(1.1);
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}

/* Upload Zone */
.upload-zone {
    transition: all 0.3s ease;
}

.upload-zone:hover {
    background: #fff3cd !important;
    border-color: #ffc107 !important;
    transform: translateY(-2px);
}

/* Cards */
.card {
    border: none;
    border-radius: 15px;
    overflow: hidden;
}

.card-header {
    border: none;
    font-weight: 600;
}

/* Buttons */
.btn {
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
}

.btn-lg {
    padding: 12px 25px;
    font-size: 16px;
}

/* Loading States */
.loading {
    opacity: 0.7;
    pointer-events: none;
}

.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

/* Responsive */
@media (max-width: 768px) {
    .result-buttons {
        grid-template-columns: 1fr;
        gap: 10px;
    }
    
    .check-item-header,
    .check-item-body {
        padding: 15px;
    }
    
    .dispenser-card {
        margin-bottom: 20px;
    }
}
</style>

<script>
$(document).ready(function() {
    // Configuration
    const config = {
        workId: {{ $workId ?? 0 }},
        stationId: {{ $stationId ?? 0 }},
        apiUrl: '{{ config("app.url") }}'
    };
    
    // State Management
    const state = {
        dispensers: [],
        currentCheck: null,
        checkItems: [],
        selectedFile: null,
        currentDetailId: null
    };

    // Initialize
    init();

    async function init() {
        try {
            await loadDispensers();
            updateOverallProgress();
            bindEvents();
        } catch (error) {
            console.error('Initialization error:', error);
            showNotification('เกิดข้อผิดพลาดในการโหลดข้อมูล', 'error');
        }
    }

    // Event Bindings
    function bindEvents() {
        // Start check modal
        $(document).on('click', '.btn-start-check', handleStartCheck);
        $('#startCheckForm').on('submit', handleStartCheckSubmit);
        
        // View check
        $(document).on('click', '.btn-view-check', handleViewCheck);
        
        // Check item results
        $(document).on('click', '.btn-result', handleResultSelection);
        
        // Image upload
        $(document).on('click', '.btn-attach-image', handleAttachImage);
        $('#btn-camera').on('click', () => $('#camera-input').click());
        $('#btn-gallery').on('click', () => $('#gallery-input').click());
        $('#camera-input, #gallery-input').on('change', handleFileSelect);
        $('#btn-upload').on('click', handleImageUpload);
        
        // Complete check
        $('#btn-complete-check').on('click', handleCompleteCheck);
        
        // Export PDF
        $('#btn-export-pdf').on('click', handleExportPDF);
        
        // Auto-save
        $('#check-remarks').on('blur', handleAutoSave);
        $(document).on('blur', '.problem-description, .inspector-notes', handleAutoSave);
    }

    // Load Dispensers
    async function loadDispensers() {
        try {
            const response = await fetch(`${config.apiUrl}/api/dispensers/${config.stationId}`);
            const result = await response.json();
            
            if (result.success) {
                state.dispensers = result.data;
                renderDispensers();
                updateOverallProgress();
            } else {
                throw new Error(result.message || 'ไม่สามารถโหลดข้อมูลตู้จ่ายได้');
            }
        } catch (error) {
            console.error('Error loading dispensers:', error);
            showError('เกิดข้อผิดพลาดในการโหลดข้อมูลตู้จ่าย');
        }
    }

    // Render Dispensers
    function renderDispensers() {
        const container = $('#dispensers-container');
        container.empty();

        if (state.dispensers.length === 0) {
            container.html(`
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-gas-pump fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">ไม่พบข้อมูลตู้จ่าย</h5>
                            <p class="text-muted">ไม่มีตู้จ่ายในสถานีนี้</p>
                        </div>
                    </div>
                </div>
            `);
            return;
        }

        state.dispensers.forEach((dispenser, index) => {
            const hasCheck = dispenser.latest_check && dispenser.latest_check.status === 'completed';
            const inProgress = dispenser.latest_check && dispenser.latest_check.status === 'draft';
            const progress = dispenser.latest_check ? dispenser.latest_check.completion_percentage : 0;
            
            let cardClass = 'pending';
            let statusBadge = '';
            let actionButton = '';
            
            if (hasCheck) {
                cardClass = 'completed';
                statusBadge = `<span class="status-badge status-completed">
                    <i class="fas fa-check-circle"></i> เสร็จสิ้น
                </span>`;
                actionButton = `
                    <button class="btn btn-info btn-view-check" 
                            data-dispenser-id="${dispenser.DispenserID}">
                        <i class="fas fa-eye"></i> ดูผลการตรวจ
                    </button>
                `;
            } else if (inProgress) {
                cardClass = 'in-progress';
                statusBadge = `<span class="status-badge status-progress">
                    <i class="fas fa-clock"></i> กำลังตรวจ
                </span>`;
                actionButton = `
                    <button class="btn btn-warning btn-view-check" 
                            data-dispenser-id="${dispenser.DispenserID}">
                        <i class="fas fa-edit"></i> ดำเนินการต่อ
                    </button>
                `;
            } else {
                statusBadge = `<span class="status-badge status-pending">
                    <i class="fas fa-hourglass-half"></i> รอตรวจ
                </span>`;
                actionButton = `
                    <button class="btn btn-primary btn-start-check" 
                            data-dispenser='${JSON.stringify(dispenser)}'>
                        <i class="fas fa-play"></i> เริ่มตรวจเช็ค
                    </button>
                `;
            }

            const dispenserCard = `
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card dispenser-card ${cardClass} h-100">
                        <div class="card-header bg-gradient-primary text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-gas-pump"></i> 
                                    ตู้ ${dispenser.DispenserNumber}
                                </h5>
                                ${statusBadge}
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-6">
                                    <small class="text-muted">ยี่ห้อ:</small>
                                    <div class="font-weight-bold">${dispenser.Brand || '-'}</div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">รุ่น:</small>
                                    <div class="font-weight-bold">${dispenser.Model || '-'}</div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <small class="text-muted">Serial Number:</small>
                                <div class="font-weight-bold">${dispenser.SerialNumber || '-'}</div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted">ความคืบหน้า:</small>
                                    <small class="font-weight-bold text-primary">${progress}%</small>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar ${hasCheck ? 'bg-success' : (inProgress ? 'bg-warning' : 'bg-secondary')}" 
                                         style="width: ${progress}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent">
                            ${actionButton}
                        </div>
                    </div>
                </div>
            `;
            
            container.append(dispenserCard);
        });
    }

    // Handle Start Check
    function handleStartCheck(e) {
        const dispenser = $(this).data('dispenser');
        
        $('#selected-dispenser-id').val(dispenser.DispenserID);
        $('#selected-dispenser-number').text(dispenser.DispenserNumber);
        $('#selected-dispenser-brand').text(dispenser.Brand || '-');
        $('#selected-dispenser-model').text(dispenser.Model || '-');
        $('#selected-dispenser-serial').text(dispenser.SerialNumber || '-');
        $('#inspector-name').val('System');
        
        $('#startCheckModal').modal('show');
    }

    // Handle Start Check Submit
    async function handleStartCheckSubmit(e) {
        e.preventDefault();
        
        const dispenserId = $('#selected-dispenser-id').val();
        const inspectorName = $('#inspector-name').val().trim();
        
        if (!inspectorName) {
            showNotification('กรุณากรอกชื่อผู้ตรวจ', 'warning');
            return;
        }

        try {
            showLoading('#startCheckForm');
            
            const response = await fetch(`${config.apiUrl}/api/dispenser-checks/start`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    work_id: config.workId,
                    station_id: config.stationId,
                    dispenser_id: dispenserId,
                    inspector_name: inspectorName
                })
            });

            const result = await response.json();
            
            if (result.success) {
                $('#startCheckModal').modal('hide');
                showNotification('เริ่มการตรวจเช็คเรียบร้อย', 'success');
                await openCheckModal(result.data.id);
            } else {
                throw new Error(result.message || 'เกิดข้อผิดพลาด');
            }
        } catch (error) {
            console.error('Error starting check:', error);
            showNotification('เกิดข้อผิดพลาดในการเริ่มตรวจเช็ค', 'error');
        } finally {
            hideLoading('#startCheckForm');
        }
    }

    // Handle View Check
    async function handleViewCheck(e) {
        const dispenserId = $(this).data('dispenser-id');
        const dispenser = state.dispensers.find(d => d.DispenserID == dispenserId);
        
        if (dispenser && dispenser.latest_check) {
            await openCheckModal(dispenser.latest_check.id, dispenser.latest_check.status === 'completed');
        }
    }

    // Open Check Modal
    async function openCheckModal(checkId, viewOnly = false) {
        try {
            showLoading('#checkModal');
            
            const response = await fetch(`${config.apiUrl}/api/dispenser-checks/${checkId}`);
            const result = await response.json();
            
            if (result.success) {
                state.currentCheck = result.data.check;
                state.checkItems = result.data.items;
                
                $('#check-dispenser-info').text(
                    `${result.data.dispenser.DispenserNumber} - ${result.data.dispenser.Brand} ${result.data.dispenser.Model}`
                );
                
                renderCheckItems(viewOnly);
                updateCheckProgress();
                
                $('#check-remarks').val(state.currentCheck.remarks || '');
                $('#check-remarks').prop('readonly', viewOnly);
                
                if (viewOnly) {
                    $('#btn-complete-check').hide();
                    $('#btn-save-progress').hide();
                } else {
                    $('#btn-complete-check').toggle(state.currentCheck.completed_items >= state.currentCheck.total_items);
                    $('#btn-save-progress').show();
                }
                
                $('#checkModal').modal('show');
            } else {
                throw new Error(result.message || 'ไม่สามารถโหลดข้อมูลได้');
            }
        } catch (error) {
            console.error('Error loading check detail:', error);
            showNotification('เกิดข้อผิดพลาดในการโหลดข้อมูล', 'error');
        } finally {
            hideLoading('#checkModal');
        }
    }

    // Render Check Items
    function renderCheckItems(viewOnly = false) {
        const container = $('#check-items-container');
        container.empty();

        state.checkItems.forEach((item, index) => {
            const checkItem = item.check_item;
            const detail = item.detail;
            const hasResult = detail !== null;
            const isProblem = hasResult && detail.result === 'problem';

            const itemHtml = `
                <div class="check-item ${hasResult ? (isProblem ? 'problem' : 'completed') : ''}" 
                     data-item-id="${checkItem.id}">
                    <div class="check-item-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="item-number">${checkItem.item_number}</span>
                                <span class="item-title">${checkItem.title}</span>
                            </div>
                            <div>
                                ${hasResult ? 
                                    `<span class="status-badge ${isProblem ? 'status-problem' : 'status-completed'}">
                                        <i class="fas fa-${isProblem ? 'exclamation-triangle' : 'check-circle'}"></i>
                                        ${isProblem ? 'พบปัญหา' : 'ปกติ'}
                                     </span>` : 
                                    '<span class="status-badge status-pending"><i class="fas fa-clock"></i> รอตรวจ</span>'
                                }
                            </div>
                        </div>
                        <div class="mt-2">
                            <small class="opacity-75">
                                <i class="fas fa-wrench"></i> อุปกรณ์: ${checkItem.equipment}
                            </small>
                        </div>
                    </div>
                    <div class="check-item-body">
                        ${!viewOnly && state.currentCheck.status !== 'completed' ? `
                            <div class="result-buttons mb-4">
                                <button type="button" class="btn btn-result btn-normal ${hasResult && detail.result === 'normal' ? 'active' : ''}"
                                        data-result="normal" data-item-id="${checkItem.id}">
                                    <i class="fas fa-check-circle"></i> การทำงานปกติ
                                </button>
                                <button type="button" class="btn btn-result btn-problem ${hasResult && detail.result === 'problem' ? 'active' : ''}"
                                        data-result="problem" data-item-id="${checkItem.id}">
                                    <i class="fas fa-exclamation-triangle"></i> พบปัญหา
                                </button>
                            </div>
                        ` : ''}
                        
                        ${isProblem ? `
                            <div class="problem-section">
                                <div class="form-group">
                                    <label class="font-weight-bold text-danger">
                                        <i class="fas fa-exclamation-triangle"></i> รายละเอียดปัญหา:
                                    </label>
                                    <textarea class="form-control problem-description" rows="3" 
                                              data-item-id="${checkItem.id}" 
                                              ${viewOnly ? 'readonly' : ''}
                                              placeholder="อธิบายปัญหาที่พบ">${detail.problem_description || ''}</textarea>
                                </div>
                                
                                ${!viewOnly && state.currentCheck.status !== 'completed' ? `
                                    <div class="text-center mb-3">
                                        <button type="button" class="btn btn-outline-warning btn-attach-image" 
                                                data-detail-id="${detail.id}" data-item-title="${checkItem.title}">
                                            <i class="fas fa-camera"></i> แนบรูปภาพปัญหา
                                        </button>
                                    </div>
                                ` : ''}
                                
                                <div class="attached-images" id="images-${detail.id}">
                                    ${detail.images ? detail.images.map(img => `
                                        <img src="${img.thumbnail_url}" class="image-thumbnail" 
                                             onclick="viewFullImage('${img.image_url}', '${img.image_description || ''}')"
                                             title="${img.image_description || 'รูปภาพปัญหา'}">
                                    `).join('') : ''}
                                </div>
                            </div>
                        ` : ''}
                        
                        <div class="form-group">
                            <label class="font-weight-bold text-secondary">
                                <i class="fas fa-sticky-note"></i> หมายเหตุเพิ่มเติม:
                            </label>
                            <textarea class="form-control inspector-notes" rows="2" 
                                      data-item-id="${checkItem.id}"
                                      ${viewOnly ? 'readonly' : ''}
                                      placeholder="หมายเหตุเพิ่มเติม (ถ้ามี)">${hasResult ? detail.inspector_notes || '' : ''}</textarea>
                        </div>
                    </div>
                </div>
            `;
            
            container.append(itemHtml);
        });
    }

    // Handle Result Selection
    function handleResultSelection(e) {
        const $btn = $(this);
        const result = $btn.data('result');
        const itemId = $btn.data('item-id');
        
        // Update UI
        $btn.siblings('.btn-result').removeClass('active');
        $btn.addClass('active');
        
        // Save result
        saveCheckItem(itemId, result);
    }

    // Save Check Item
    async function saveCheckItem(itemId, result) {
        const problemDescription = $(`.problem-description[data-item-id="${itemId}"]`).val() || '';
        const inspectorNotes = $(`.inspector-notes[data-item-id="${itemId}"]`).val() || '';
        
        try {
            const response = await fetch(`${config.apiUrl}/api/dispenser-checks/${state.currentCheck.id}/save-item`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    check_item_id: itemId,
                    result: result,
                    problem_description: problemDescription,
                    inspector_notes: inspectorNotes
                })
            });

            const apiResult = await response.json();
            
            if (apiResult.success) {
                // Update local state
                const itemIndex = state.checkItems.findIndex(item => item.check_item.id === itemId);
                if (itemIndex !== -1) {
                    state.checkItems[itemIndex].detail = apiResult.data;
                }
                
                // Update UI
                const $checkItem = $(`.check-item[data-item-id="${itemId}"]`);
                $checkItem.removeClass('completed problem');
                
                if (result === 'problem') {
                    $checkItem.addClass('problem');
                    showProblemSection(itemId, apiResult.data.id);
                } else {
                    $checkItem.addClass('completed');
                    hideProblemSection(itemId);
                }
                
                // Update progress
                state.currentCheck.completed_items++;
                updateCheckProgress();
                
                // Show complete button if ready
                if (state.currentCheck.completed_items >= state.currentCheck.total_items) {
                    $('#btn-complete-check').show();
                }
                
                showNotification('บันทึกผลการตรวจเรียบร้อย', 'success');
                
            } else {
                throw new Error(apiResult.message || 'เกิดข้อผิดพลาด');
            }
        } catch (error) {
            console.error('Error saving check item:', error);
            showNotification('เกิดข้อผิดพลาดในการบันทึก', 'error');
        }
    }

    // Show/Hide Problem Section
    function showProblemSection(itemId, detailId) {
        const $checkItem = $(`.check-item[data-item-id="${itemId}"]`);
        const $body = $checkItem.find('.check-item-body');
        
        if (!$body.find('.problem-section').length) {
            const problemHtml = `
                <div class="problem-section">
                    <div class="form-group">
                        <label class="font-weight-bold text-danger">
                            <i class="fas fa-exclamation-triangle"></i> รายละเอียดปัญหา:
                        </label>
                        <textarea class="form-control problem-description" rows="3" 
                                  data-item-id="${itemId}" 
                                  placeholder="อธิบายปัญหาที่พบ"></textarea>
                    </div>
                    
                    <div class="text-center mb-3">
                        <button type="button" class="btn btn-outline-warning btn-attach-image" 
                                data-detail-id="${detailId}" data-item-title="">
                            <i class="fas fa-camera"></i> แนบรูปภาพปัญหา
                        </button>
                    </div>
                    
                    <div class="attached-images" id="images-${detailId}">
                        <!-- รูปภาพจะแสดงที่นี่ -->
                    </div>
                </div>
            `;
            
            $body.find('.inspector-notes').parent().before(problemHtml);
        }
    }

    function hideProblemSection(itemId) {
        $(`.check-item[data-item-id="${itemId}"] .problem-section`).remove();
    }

    // Handle Attach Image
    function handleAttachImage(e) {
        state.currentDetailId = $(this).data('detail-id');
        const itemTitle = $(this).data('item-title');
        
        $('#current-detail-id').val(state.currentDetailId);
        $('#image-item-title').text(itemTitle);
        
        resetImageUpload();
        loadExistingImages();
        $('#imageModal').modal('show');
    }

    // Handle File Select
    function handleFileSelect(e) {
        const file = e.target.files[0];
        if (!file) return;

        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            showNotification('กรุณาเลือกไฟล์รูปภาพที่ถูกต้อง', 'warning');
            return;
        }

        if (file.size > 10 * 1024 * 1024) {
            showNotification('ขนาดไฟล์ต้องไม่เกิน 10MB', 'warning');
            return;
        }

        state.selectedFile = file;
        showImagePreview(file);
        $('#btn-upload').show();
    }

    // Show Image Preview
    function showImagePreview(file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            $('#preview-image').attr('src', e.target.result);
            $('#image-preview').show();
        };
        reader.readAsDataURL(file);
    }

    // Handle Image Upload
    async function handleImageUpload() {
        if (!state.selectedFile || !state.currentDetailId) {
            showNotification('กรุณาเลือกรูปภาพ', 'warning');
            return;
        }

        const formData = new FormData();
        formData.append('detail_id', state.currentDetailId);
        formData.append('image_type', 'problem');
        formData.append('image', state.selectedFile);
        formData.append('image_description', $('#image-description').val());

        try {
            showLoading('#btn-upload');
            $('#upload-text').text('กำลังอัพโหลด...');

            const response = await fetch(`${config.apiUrl}/api/dispenser-checks/upload-image`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (result.success) {
                showNotification('อัพโหลดรูปภาพเรียบร้อย', 'success');
                resetImageUpload();
                loadExistingImages();
                
                // Update main modal
                const $imagesContainer = $(`#images-${state.currentDetailId}`);
                $imagesContainer.append(`
                    <img src="${result.data.thumbnail_url}" class="image-thumbnail" 
                         onclick="viewFullImage('${result.data.image_url}', '${result.data.image_description || ''}')"
                         title="${result.data.image_description || 'รูปภาพปัญหา'}">
                `);
            } else {
                throw new Error(result.message || 'เกิดข้อผิดพลาด');
            }
        } catch (error) {
            console.error('Upload error:', error);
            showNotification('เกิดข้อผิดพลาดในการอัพโหลด', 'error');
        } finally {
            hideLoading('#btn-upload');
            $('#upload-text').text('อัพโหลดรูปภาพ');
        }
    }

    // Handle Complete Check
    async function handleCompleteCheck() {
        if (!confirm('คุณต้องการเสร็จสิ้นการตรวจเช็คนี้หรือไม่?\n\nเมื่อเสร็จสิ้นแล้วจะไม่สามารถแก้ไขได้')) {
            return;
        }

        try {
            showLoading('#btn-complete-check');

            const response = await fetch(`${config.apiUrl}/api/dispenser-checks/${state.currentCheck.id}/complete`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    remarks: $('#check-remarks').val()
                })
            });

            const result = await response.json();

            if (result.success) {
                showNotification('เสร็จสิ้นการตรวจเช็คเรียบร้อย', 'success');
                $('#checkModal').modal('hide');
                await loadDispensers();
                updateOverallProgress();
            } else {
                throw new Error(result.message || 'เกิดข้อผิดพลาด');
            }
        } catch (error) {
            console.error('Error completing check:', error);
            showNotification('เกิดข้อผิดพลาดในการเสร็จสิ้น', 'error');
        } finally {
            hideLoading('#btn-complete-check');
        }
    }

    // Handle Export PDF
    function handleExportPDF() {
        window.open(`${config.apiUrl}/api/dispenser-checks/${config.workId}/${config.stationId}/export-pdf`, '_blank');
    }

    // Handle Auto Save
    async function handleAutoSave(e) {
        // Implementation for auto-saving notes
        const $input = $(e.target);
        const value = $input.val();
        
        if (value !== $input.data('last-value')) {
            $input.data('last-value', value);
            // Auto-save logic here
        }
    }

    // Update Check Progress
    function updateCheckProgress() {
        if (!state.currentCheck) return;

        const percentage = state.currentCheck.total_items > 0 ? 
            Math.round((state.currentCheck.completed_items / state.currentCheck.total_items) * 100) : 0;
        
        $('#progress-text').text(`${state.currentCheck.completed_items}/${state.currentCheck.total_items} (${percentage}%)`);
        $('#progress-bar').css('width', `${percentage}%`);
        
        $('#normal-count').text(state.currentCheck.normal_items || 0);
        $('#problem-count').text(state.currentCheck.problem_items || 0);
        $('#remaining-count').text(state.currentCheck.total_items - state.currentCheck.completed_items);
    }

    // Update Overall Progress
    function updateOverallProgress() {
        const total = state.dispensers.length;
        const completed = state.dispensers.filter(d => 
            d.latest_check && d.latest_check.status === 'completed'
        ).length;
        
        const percentage = total > 0 ? Math.round((completed / total) * 100) : 0;
        
        $('#total-dispensers').text(total);
        $('#completed-dispensers').text(completed);
        $('#overall-progress').css('width', `${percentage}%`);
        $('#overall-progress-text').text(`${percentage}%`);
        
        // Show export button if all completed
        if (completed === total && total > 0) {
            $('#btn-export-pdf').show();
        } else {
            $('#btn-export-pdf').hide();
        }
    }

    // Utility Functions
    function resetImageUpload() {
        state.selectedFile = null;
        $('#image-preview').hide();
        $('#btn-upload').hide();
        $('#camera-input, #gallery-input').val('');
        $('#image-description').val('');
    }

    function loadExistingImages() {
        // Load existing images for the current detail
        const $container = $('#existing-images');
        $container.html('<div class="col-12 text-center"><i class="fas fa-spinner fa-spin"></i> กำลังโหลด...</div>');
        
        // Implementation would fetch and display existing images
        setTimeout(() => {
            $container.html('<div class="col-12 text-center text-muted">ไม่มีรูปภาพ</div>');
        }, 1000);
    }

    function showLoading(selector) {
        $(selector).addClass('loading').prop('disabled', true);
        $(`${selector} .spinner-border`).show();
    }

    function hideLoading(selector) {
        $(selector).removeClass('loading').prop('disabled', false);
        $(`${selector} .spinner-border`).hide();
    }

    function showNotification(message, type = 'info') {
        // Simple notification system
        const alertClass = type === 'error' ? 'danger' : type;
        const notification = $(`
            <div class="alert alert-${alertClass} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `);
        
        $('body').append(notification);
        setTimeout(() => notification.alert('close'), 5000);
    }

    function showError(message) {
        showNotification(message, 'error');
    }

    // Global function for viewing full image
    window.viewFullImage = function(imageUrl, description) {
        const modal = `
            <div class="modal fade" id="imageViewModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">รูปภาพปัญหา</h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body text-center">
                            <img src="${imageUrl}" class="img-fluid rounded shadow">
                            ${description ? `<p class="mt-3 text-muted">${description}</p>` : ''}
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $('#imageViewModal').remove();
        $('body').append(modal);
        $('#imageViewModal').modal('show');
        $('#imageViewModal').on('hidden.bs.modal', function() {
            $(this).remove();
        });
    }
});
</script>
@endsection