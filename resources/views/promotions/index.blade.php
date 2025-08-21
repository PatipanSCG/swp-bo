@extends('layouts.admin')
@section('title', 'จัดการโปรโมชั่น')

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.9.0/dist/sweetalert2.min.css">
<style>
    .modal-xl {
        max-width: 90%;
    }
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 1rem;
    }
    .btn-group .btn {
        margin-right: 2px;
    }
    .status-badge {
        font-size: 0.8rem;
    }
</style>
@endsection

@section('main-content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="h3 mb-0">
                <i class="fas fa-tags text-primary"></i> จัดการโปรโมชั่น
            </h2>
            <p class="text-muted">จัดการข้อมูลโปรโมชั่นและส่วนลด</p>
        </div>
        <div class="col-md-6 text-end">
            <button type="button" class="btn btn-success" id="addPromotionBtn">
                <i class="fas fa-plus"></i> เพิ่มโปรโมชั่นใหม่
            </button>
            <button type="button" class="btn btn-danger" id="bulkDeleteBtn" style="display: none;">
                <i class="fas fa-trash"></i> ลบที่เลือก
            </button>
        </div>
    </div>

    <!-- DataTable Card -->
    <div class="card shadow">
        <div class="card-header bg-white">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="card-title mb-0">รายการโปรโมชั่น</h5>
                </div>
                <div class="col-auto">
                    <div class="input-group">
                        <input type="text" class="form-control" id="searchInput" placeholder="ค้นหา...">
                        <button class="btn btn-outline-secondary" type="button" id="refreshBtn">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="promotionsTable" class="table table-striped table-hover w-100">
                    <thead class="table-dark">
                        <tr>
                            <th width="30">
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                            <th>ID</th>
                            <th>ประเภท</th>
                            <th>รายละเอียด</th>
                            <th>จำนวน</th>
                            <th>ราคา/หน่วย</th>
                            <th>ราคารวม</th>
                            <th>สถานะ</th>
                            <th>วันที่สร้าง</th>
                            <th width="120">จัดการ</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal สำหรับเพิ่ม/แก้ไข -->
<div class="modal fade" id="promotionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">เพิ่มโปรโมชั่นใหม่</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="promotionForm">
                <div class="modal-body">
                    <div class="row">
                        <!-- ข้อมูลพื้นฐาน -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">สถานะ *</label>
                                <select class="form-select" name="status" required>
                                    <option value="active">เปิดใช้งาน</option>
                                    <option value="inactive">ปิดใช้งาน</option>
                                    <option value="expired">หมดอายุ</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">รายละเอียด *</label>
                        <textarea class="form-control" name="detail" rows="3" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">จำนวน *</label>
                                <input type="number" class="form-control" name="quantity" min="1" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">ราคาต่อหน่วย (บาท) *</label>
                                <input type="number" class="form-control" name="unit_price" step="0.01" min="0" required>
                            </div>
                        </div>
                    </div>

                    <!-- แสดงราคารวม -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <strong>ราคารวม:</strong> <span id="totalValue">0.00</span> บาท
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> บันทึก
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal สำหรับดูรายละเอียด -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">รายละเอียดโปรโมชั่น</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.9.0/dist/sweetalert2.all.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable
    let table = $('#promotionsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("promotions.datatable") }}',
            type: 'GET'
        },
        columns: [
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return '<input type="checkbox" class="row-checkbox" value="' + row.id + '">';
                }
            },
            { data: 'id', name: 'id' },
            { data: 'type', name: 'type' },
            { data: 'detail', name: 'detail' },
            { data: 'quantity', name: 'quantity', className: 'text-center' },
            { data: 'unit_price_formatted', name: 'unit_price', className: 'text-end' },
            { data: 'total_value', name: 'total_value', className: 'text-end' },
            { data: 'status_badge', name: 'status', className: 'text-center' },
            { data: 'created_at', name: 'created_at', className: 'text-center' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
        ],
        order: [[1, 'desc']],
        pageLength: 25,
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/th.json'
        }
    });

    // Custom search
    $('#searchInput').on('keyup', function() {
        table.search(this.value).draw();
    });

    // Refresh button
    $('#refreshBtn').click(function() {
        table.ajax.reload();
    });

    // Select all checkbox
    $('#selectAll').change(function() {
        $('.row-checkbox').prop('checked', this.checked);
        toggleBulkDeleteBtn();
    });

    // Individual checkbox change
    $(document).on('change', '.row-checkbox', function() {
        toggleBulkDeleteBtn();
    });

    // Toggle bulk delete button
    function toggleBulkDeleteBtn() {
        const checkedCount = $('.row-checkbox:checked').length;
        if (checkedCount > 0) {
            $('#bulkDeleteBtn').show();
        } else {
            $('#bulkDeleteBtn').hide();
        }
    }

    // Calculate total value
    function calculateTotal() {
        const quantity = parseFloat($('input[name="quantity"]').val()) || 0;
        const unitPrice = parseFloat($('input[name="unit_price"]').val()) || 0;
        const total = quantity * unitPrice;
        $('#totalValue').text(total.toLocaleString('th-TH', { minimumFractionDigits: 2 }));
    }

    // Auto calculate on input change
    $(document).on('input', 'input[name="quantity"], input[name="unit_price"]', calculateTotal);

    // Add new promotion
    $('#addPromotionBtn').click(function() {
        $('#modalTitle').text('เพิ่มโปรโมชั่นใหม่');
        $('#promotionForm')[0].reset();
        $('#promotionForm').data('mode', 'create');
        calculateTotal();
        $('#promotionModal').modal('show');
    });

    // Edit promotion
    window.editPromotion = function(id) {
        $.get('{{ route("promotions.show", ":id") }}'.replace(':id', id))
            .done(function(response) {
                if (response.success) {
                    const data = response.data;
                    $('#modalTitle').text('แก้ไขโปรโมชั่น');
                    $('#promotionForm').data('mode', 'edit');
                    $('#promotionForm').data('id', id);
                    
                    $('input[name="type"]').val(data.type);
                    $('textarea[name="detail"]').val(data.detail);
                    $('input[name="quantity"]').val(data.quantity);
                    $('input[name="unit_price"]').val(data.unit_price);
                    $('select[name="status"]').val(data.status);
                    
                    calculateTotal();
                    $('#promotionModal').modal('show');
                }
            })
            .fail(function() {
                Swal.fire('ข้อผิดพลาด!', 'ไม่สามารถโหลดข้อมูลได้', 'error');
            });
    };

    // View promotion
    window.viewPromotion = function(id) {
        $.get('{{ route("promotions.show", ":id") }}'.replace(':id', id))
            .done(function(response) {
                if (response.success) {
                    const data = response.data;
                    const statusBadge = getStatusBadge(data.status);
                    const totalValue = (data.quantity * data.unit_price).toLocaleString('th-TH', { minimumFractionDigits: 2 });
                    
                    $('#viewContent').html(`
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table">
                                    <tr>
                                        <th width="40%">ID:</th>
                                        <td>${data.id}</td>
                                    </tr>
                                    <tr>
                                        <th>ประเภท:</th>
                                        <td>${data.type}</td>
                                    </tr>
                                    <tr>
                                        <th>สถานะ:</th>
                                        <td>${statusBadge}</td>
                                    </tr>
                                    <tr>
                                        <th>จำนวน:</th>
                                        <td>${data.quantity.toLocaleString()} หน่วย</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table">
                                    <tr>
                                        <th width="40%">ราคา/หน่วย:</th>
                                        <td>฿${parseFloat(data.unit_price).toLocaleString('th-TH', { minimumFractionDigits: 2 })}</td>
                                    </tr>
                                    <tr>
                                        <th>ราคารวม:</th>
                                        <td><strong>฿${totalValue}</strong></td>
                                    </tr>
                                    <tr>
                                        <th>วันที่สร้าง:</th>
                                        <td>${new Date(data.created_at).toLocaleDateString('th-TH')}</td>
                                    </tr>
                                    <tr>
                                        <th>วันที่แก้ไข:</th>
                                        <td>${new Date(data.updated_at).toLocaleDateString('th-TH')}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <h6>รายละเอียด:</h6>
                                <div class="alert alert-light">${data.detail}</div>
                            </div>
                        </div>
                    `);
                    $('#viewModal').modal('show');
                }
            })
            .fail(function() {
                Swal.fire('ข้อผิดพลาด!', 'ไม่สามารถโหลดข้อมูลได้', 'error');
            });
    };

    // Delete promotion
    window.deletePromotion = function(id) {
        Swal.fire({
            title: 'ยืนยันการลบ',
            text: 'คุณต้องการลบโปรโมชั่นนี้หรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'ลบ',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("promotions.destroy", ":id") }}'.replace(':id', id),
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                })
                .done(function(response) {
                    if (response.success) {
                        Swal.fire('สำเร็จ!', response.message, 'success');
                        table.ajax.reload();
                    } else {
                        Swal.fire('ข้อผิดพลาด!', response.message, 'error');
                    }
                })
                .fail(function() {
                    Swal.fire('ข้อผิดพลาด!', 'เกิดข้อผิดพลาดในการลบข้อมูล', 'error');
                });
            }
        });
    };

    // Bulk delete
    $('#bulkDeleteBtn').click(function() {
        const selectedIds = $('.row-checkbox:checked').map(function() {
            return this.value;
        }).get();

        if (selectedIds.length === 0) {
            Swal.fire('แจ้งเตือน', 'กรุณาเลือกรายการที่ต้องการลบ', 'warning');
            return;
        }

        Swal.fire({
            title: 'ยืนยันการลบ',
            text: `คุณต้องการลบโปรโมชั่น ${selectedIds.length} รายการที่เลือกหรือไม่?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'ลบ',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("promotions.bulk-delete") }}',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: { ids: selectedIds }
                })
                .done(function(response) {
                    if (response.success) {
                        Swal.fire('สำเร็จ!', response.message, 'success');
                        table.ajax.reload();
                        $('#selectAll').prop('checked', false);
                        toggleBulkDeleteBtn();
                    } else {
                        Swal.fire('ข้อผิดพลาด!', response.message, 'error');
                    }
                })
                .fail(function() {
                    Swal.fire('ข้อผิดพลาด!', 'เกิดข้อผิดพลาดในการลบข้อมูล', 'error');
                });
            }
        });
    });

    // Form submission
    $('#promotionForm').submit(function(e) {
        e.preventDefault();
        
        const mode = $(this).data('mode');
        const formData = new FormData(this);
        
        let url, method;
        if (mode === 'create') {
            url = '{{ route("promotions.store") }}';
            method = 'POST';
        } else {
            const id = $(this).data('id');
            url = '{{ route("promotions.update", ":id") }}'.replace(':id', id);
            method = 'PUT';
            formData.append('_method', 'PUT');
        }

        // Add CSRF token
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        $.ajax({
            url: url,
            type: method,
            data: formData,
            processData: false,
            contentType: false
        })
        .done(function(response) {
            if (response.success) {
                Swal.fire('สำเร็จ!', response.message, 'success');
                $('#promotionModal').modal('hide');
                table.ajax.reload();
            } else {
                Swal.fire('ข้อผิดพลาด!', response.message, 'error');
            }
        })
        .fail(function(xhr) {
            if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;
                let errorMessage = '';
                Object.keys(errors).forEach(function(key) {
                    errorMessage += errors[key][0] + '\n';
                });
                Swal.fire('ข้อมูลไม่ถูกต้อง', errorMessage, 'error');
            } else {
                Swal.fire('ข้อผิดพลาด!', 'เกิดข้อผิดพลาดในการบันทึกข้อมูล', 'error');
            }
        });
    });

    // Helper function for status badge
    function getStatusBadge(status) {
        const badges = {
            'active': '<span class="badge bg-success">เปิดใช้งาน</span>',
            'inactive': '<span class="badge bg-secondary">ปิดใช้งาน</span>',
            'expired': '<span class="badge bg-danger">หมดอายุ</span>'
        };
        return badges[status] || '<span class="badge bg-warning">ไม่ระบุ</span>';
    }
});
</script>
@endsection-3">
                                <label class="form-label">ประเภทโปรโมชั่น *</label>
                                <input type="text" class="form-control" name="type" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb@extends('layouts.app')

@section('title', 'จัดการโปรโมชั่น')

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.9.0/dist/sweetalert2.min.css">
<style>
    .promotion-image {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
    }
    .modal-xl {
        max-width: 90%;
    }
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 1rem;
    }
    .btn-group .btn {
        margin-right: 2px;
    }
    .status-badge {
        font-size: 0.8rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="h3 mb-0">
                <i class="fas fa-tags text-primary"></i> จัดการโปรโมชั่น
            </h2>
            <p class="text-muted">จัดการข้อมูลโปรโมชั่นและส่วนลด</p>
        </div>
        <div class="col-md-6 text-end">
            <button type="button" class="btn btn-success" id="addPromotionBtn">
                <i class="fas fa-plus"></i> เพิ่มโปรโมชั่นใหม่
            </button>
            <button type="button" class="btn btn-danger" id="bulkDeleteBtn" style="display: none;">
                <i class="fas fa-trash"></i> ลบที่เลือก
            </button>
        </div>
    </div>

    <!-- DataTable Card -->
    <div class="card shadow">
        <div class="card-header bg-white">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="card-title mb-0">รายการโปรโมชั่น</h5>
                </div>
                <div class="col-auto">
                    <div class="input-group">
                        <input type="text" class="form-control" id="searchInput" placeholder="ค้นหา...">
                        <button class="btn btn-outline-secondary" type="button" id="refreshBtn">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="promotionsTable" class="table table-striped table-hover w-100">
                    <thead class="table-dark">
                        <tr>
                            <th width="30">
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                            <th>ID</th>
                            <th>ประเภท</th>
                            <th>รายละเอียด</th>
                            <th>จำนวน</th>
                            <th>ราคา/หน่วย</th>
                            <th>ข้อมูลราคา</th>
                            <th>สถานะ</th>
                            <th>ช่วงวันที่</th>
                            <th>วันที่สร้าง</th>
                            <th width="120">จัดการ</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal สำหรับเพิ่ม/แก้ไข -->
<div class="modal fade" id="promotionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">เพิ่มโปรโมชั่นใหม่</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="promotionForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <!-- ข้อมูลพื้นฐาน -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">ประเภทโปรโมชั่น *</label>
                                <input type="text" class="form-control" name="type" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">สถานะ *</label>
                                <select class="form-select" name="status" required>
                                    <option value="active">เปิดใช้งาน</option>
                                    <option value="inactive">ปิดใช้งาน</option>
                                    <option value="expired">หมดอายุ</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">รายละเอียด *</label>
                        <textarea class="form-control" name="detail" rows="3" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">จำนวน *</label>
                                <input type="number" class="form-control" name="quantity" min="1" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">ราคาต่อหน่วย (บาท) *</label>
                                <input type="number" class="form-control" name="unit_price" step="0.01" min="0" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">วันที่เริ่มต้น</label>
                                <input type="date" class="form-control" name="start_date">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">วันที่สิ้นสุด</label>
                                <input type="date" class="form-control" name="end_date">
                            </div>
                        </div>
                    </div>

                    <!-- ส่วนลด -->
                    <div class="card bg-light mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">ข้อมูลส่วนลด (ถ้ามี)</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">เปอร์เซ็นต์ส่วนลด (%)</label>
                                        <input type="number" class="form-control" name="discount_percentage" step="0.01" min="0" max="100">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">ส่วนลดสูงสุด (บาท)</label>
                                        <input type="number" class="form-control" name="max_discount_amount" step="0.01" min="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- รูปภาพ -->