@extends('layouts.admin')

@section('main-content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">รายการใบเสนอราคา</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary" id="btn-create-quotation">
                            <i class="fas fa-plus"></i> สร้างใบเสนอราคาใหม่
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="filter-status">สถานะ</label>
                            <select class="form-control" id="filter-status">
                                <option value="">ทั้งหมด</option>
                                <option value="draft">ร่าง</option>
                                <option value="sent">ส่งแล้ว</option>
                                <option value="approved">อนุมัติแล้ว</option>
                                <option value="rejected">ปฏิเสธ</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filter-date-from">วันที่เริ่มต้น</label>
                            <input type="date" class="form-control" id="filter-date-from">
                        </div>
                        <div class="col-md-3">
                            <label for="filter-date-to">วันที่สิ้นสุด</label>
                            <input type="date" class="form-control" id="filter-date-to">
                        </div>
                        <div class="col-md-3">
                            <label>&nbsp;</label>
                            <div>
                                <button type="button" class="btn btn-info" id="btn-search">
                                    <i class="fas fa-search"></i> ค้นหา
                                </button>
                                <button type="button" class="btn btn-secondary" id="btn-reset">
                                    <i class="fas fa-undo"></i> รีเซ็ต
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- DataTable -->
                    <div class="table-responsive">
                        <table id="quotations-table" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>เลขที่เอกสาร</th>
                                    <th>เลขที่อ้างอิง</th>
                                    <th>ลูกค้า</th>
                                    <th>สถานี</th>
                                    <th>จำนวนเงิน</th>
                                    <th>สถานะ</th>
                                    <th>วันที่ออก</th>
                                    <th>วันที่สร้าง</th>
                                    <th>จัดการ</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#quotations-table').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: window.APP_URL+'/api/quotations',
            data: function(d) {
                d.status = $('#filter-status').val();
                d.date_from = $('#filter-date-from').val();
                d.date_to = $('#filter-date-to').val();
            }
        },
        columns: [
            { data: 'DocNo', name: 'DocNo' },
            { data: 'QuotationNo', name: 'QuotationNo' },
            { data: 'CustomerName', name: 'CustomerName' },
            { data: 'StationName', name: 'StationName' },
            { 
                data: 'GrandTotal', 
                name: 'GrandTotal',
                className: 'text-right'
            },
            { 
                data: 'Status', 
                name: 'Status',
                render: function(data) {
                    var badges = {
                        'draft': 'badge-secondary',
                        'sent': 'badge-info', 
                        'approved': 'badge-success',
                        'rejected': 'badge-danger'
                    };
                    var texts = {
                        'draft': 'ร่าง',
                        'sent': 'ส่งแล้ว',
                        'approved': 'อนุมัติแล้ว', 
                        'rejected': 'ปฏิเสธ'
                    };
                    return '<span class="badge ' + (badges[data] || 'badge-secondary') + '">' + 
                           (texts[data] || data) + '</span>';
                }
            },
            { data: 'IssuedDate', name: 'IssuedDate' },
            { data: 'created_at', name: 'created_at' },
            { 
                data: 'actions', 
                name: 'actions',
                orderable: false,
                searchable: false,
                className: 'text-center'
            }
        ],
        order: [[7, 'desc']], // Order by created_at desc
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Thai.json'
        }
    });

    // Search functionality
    $('#btn-search').click(function() {
        table.ajax.reload();
    });

    // Reset filters
    $('#btn-reset').click(function() {
        $('#filter-status').val('');
        $('#filter-date-from').val('');
        $('#filter-date-to').val('');
        table.ajax.reload();
    });

    // Create new quotation (implement based on your existing modal)
    $('#btn-create-quotation').click(function() {
        $('#quotationModal').modal('show');
        // Reset form and load fresh data
        resetQuotationModal();
    });

    // Handle actions (view, edit, delete, download PDF)
    $(document).on('click', '.btn-view-quotation', function() {
        var id = $(this).data('id');
        window.open(window.APP_URL+'/quotations/' + id, '_blank');
    });

    $(document).on('click', '.btn-edit-quotation', function() {
        var id = $(this).data('id');
        window.location.href = window.APP_URL+'/quotations/' + id + '/edit';
    });

    $(document).on('click', '.btn-download-pdf', function() {
        var id = $(this).data('id');
        window.open(window.APP_URL+'/quotations/' + id + '/pdf', '_blank');
    });

    $(document).on('click', '.btn-delete-quotation', function() {
        var id = $(this).data('id');
        var docNo = $(this).data('doc-no');
        
        Swal.fire({
            title: 'ยืนยันการลบ',
            text: 'คุณต้องการลบใบเสนอราคา ' + docNo + ' ใช่หรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'ลบ',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: window.APP_URL+'/quotations/' + id,
                    method: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('สำเร็จ!', response.message, 'success');
                            table.ajax.reload();
                        }
                    },
                    error: function(xhr) {
                        var message = xhr.responseJSON ? xhr.responseJSON.message : 'เกิดข้อผิดพลาด';
                        Swal.fire('เกิดข้อผิดพลาด!', message, 'error');
                    }
                });
            }
        });
    });
});

function resetQuotationModal() {
    // Reset your quotation modal form here
    // This function should reset all fields in the quotation modal
}
</script>
@endsection