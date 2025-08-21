@extends('layouts.admin')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<style>
    .card-header {
        cursor: pointer;
    }

    .card-header:hover {
        background-color: #f1f1f1;
    }
</style>

@section('main-content')
<input type="hidden" id="ip-last" value="{{$station->last}}">
<input type="hidden" id="ip-long" value="{{$station->long}}">
<input type="hidden" value="{{$station->StationID}}" id="stationid">

<div class="container-fluid">
    <div class="row">
        @include('modals.station_edit')
        <!-- ส่วนที่ 1: ซ้าย (4 การ์ด) -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">ข้อมูลสถานี</h6>
                    <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editStationModal">
                        ✏️ แก้ไข
                    </button>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 text-primary text-right">ชื่อสถานี :</div>
                        <div class="col-6">{{$station->StationName ?? '-' }}</div>
                    </div>
                    <div class="row">
                        <div class="col-6  text-primary text-right">เลขประจำตัวผู้เสียภาษี :</div>
                        <div class="col-6">{{$station->TaxID ?? '-' }}</div>
                    </div>
                    <div class="row">
                        <div class="col-6  text-primary text-right">ที่อยู่ :</div>
                        <div class="col-6">{{$station->Address ?? '-' }}</div>
                    </div>
                    <div class="row">
                        <div class="col-6  text-primary text-right">ตำบล :</div>
                        <div class="col-6">{{$station->subdistrict->NameInThai ?? '-' }}</div>
                    </div>
                    <div class="row">
                        <div class="col-6  text-primary text-right">อำเภอ :</div>
                        <div class="col-6">{{$station->district->NameInThai ?? '-' }}</div>
                    </div>
                    <div class="row">
                        <div class="col-6  text-primary text-right">จังหวัด :</div>
                        <div class="col-6">{{$station->province->NameInThai ?? '-' }}</div>
                    </div>
                    <div class="row">
                        <div class="col-6  text-primary text-right">รหัสไปรษณีย์ :</div>
                        <div class="col-6">{{$station->subdistrict->ZipCode ?? '-' }}</div>
                    </div>
                    <div class="row">
                        <div class="col-6  text-primary text-right">จำนวนตู้จ่าย :</div>
                        <div class="col-6">{{$station->dispensers_count ?? '-' }}</div>
                    </div>
                    <div class="row">
                        <div class="col-6  text-primary text-right">จำนวนหัวจ่าย :</div>
                        <div class="col-6">{{$station->nozzle_count ?? '-' }}</div>
                    </div>
                    <button class="btn btn-primary" id="btn-add-location-modal">
                        + แผนที่
                    </button>
                    <button class="btn btn-primary" id="btn-show-map">
                        📍 ดูแผนที่
                    </button>
                    <button class="btn btn-primary" id="btn-navigate">
                        🧭 นำทางไปยังจุดหมาย
                    </button>
                    @include('modals.view_map')
                </div>
            </div>
            <div class="card shadow mb-4">
                <div class="card-header" data-toggle="collapse" data-target="#collapseCard-1" aria-expanded="false" aria-controls="collapseCard">
                    <h6 class="m-0 font-weight-bold text-primary">ข้อมูลการออกใบกำกับภาษี</h6>
                </div>
                <div id="collapseCard-1" class="collapse">
                    <div class="card-body">
                        @if ($customer)
                        <div class="text-right">
                            <button class="btn btn-warning" data-toggle="modal" data-target="#editCustomerModal">✏️ แก้ไขข้อมูลลูกค้า</button>
                        </div>
                        <div class="row">
                            <div class="col-6 text-primary text-right">ชื่อลูกค้า :</div>
                            <div class="col-6">{{$customer->CustomerName ?? '-' }}</div>
                        </div>
                        <div class="row">
                            <div class="col-6 text-primary text-right">เลขประจำตัวผู้เสียภาษี :</div>
                            <div class="col-6">{{$customer->TaxID ?? '-' }}</div>
                        </div>
                        <div class="row">
                            <div class="col-6 text-primary text-right">ที่อยู่ :</div>
                            <div class="col-6">{{$customer->Address ?? '-' }}</div>
                        </div>
                        <div class="row">
                            <div class="col-6 text-primary text-right">ตำบล :</div>
                            <div class="col-6">{{$customer->subdistrict->NameInThai ?? '-' }}</div>
                        </div>
                        <div class="row">
                            <div class="col-6 text-primary text-right">อำเภอ :</div>
                            <div class="col-6">{{$customer->district->NameInThai ?? '-' }}</div>
                        </div>
                        <div class="row">
                            <div class="col-6 text-primary text-right">จังหวัด :</div>
                            <div class="col-6">{{$customer->province->NameInThai ?? '-' }}</div>
                        </div>
                        <div class="row">
                            <div class="col-6 text-primary text-right">รหัสไปรษณีย์ :</div>
                            <div class="col-6">{{$customer->Postcode ?? '-' }}</div>
                        </div>
                        <div class="row">
                            <div class="col-6 text-primary text-right">เบอร์โทร :</div>
                            <div class="col-6">{{$customer->Phone ?? '-' }}</div>
                        </div>
                        <div class="row">
                            <div class="col-6 text-primary text-right">อีเมล :</div>
                            <div class="col-6">{{$customer->Email ?? '-' }}</div>
                        </div>
                        @include('modals.customer_add', ['customer' => $customer])
                        @else
                        {{-- ยังไม่มีข้อมูล --}}
                        <div class="alert alert-info">
                            ยังไม่มีข้อมูลลูกค้า
                        </div>
                        <div class="text-right">
                            <button class="btn btn-success" data-toggle="modal" data-target="#addCustomerModal">➕ เพิ่มข้อมูลลูกค้า</button>
                            <button class="btn btn-secondary" id="copyFromStationBtn"> ใช้ข้อมูลเดียวกับปั๊ม</button>
                        </div>

                        {{-- Modal เพิ่ม --}}
                        @include('modals.customer_add')
                        @endif
                    </div>
                </div>

            </div>
            @include('modals.add_communication')

            <div class="card shadow mb-4">
                <div class="card-header" data-toggle="collapse" data-target="#collapseCard-2" aria-expanded="false" aria-controls="collapseCard-2">
                    <h6 class="m-0 font-weight-bold text-primary">ข้อมูลการติดต่อ</h6>
                </div>

                <div id="collapseCard-2" class="collapse">
                    <div class="card-body">
                        <div class="mb-3 text-right">
                            <button class="btn btn-success" data-toggle="modal" data-target="#addContactModal">➕ เพิ่มผู้ติดต่อใหม่</button>
                        </div>
                        <div class="table-responsive">
                            <table id="contactsTable" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>ชื่อผู้ติดต่อ</th>
                                        <th>อีเมล</th>
                                        <th>เบอร์โทร</th>
                                        <th>action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ส่วนที่ 2: ขวา (การ์ดใหญ่) -->
        <div class="col-lg-8">
            <div class="row mb-2">

            </div>
            <div class="card shadow mb-4 full-height">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">กิจกรรมการติดต่อ</h6>
                    <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#addComModal">
                        ติดต่อลูกค้า
                    </button>
                    <button class="btn btn-danger" id="btn-qt" data-toggle="modal">
                        ออกใบเสนอราคา
                    </button>
                    @include('modals.qt_detail')
                    @include('modals.addlocation')

                    <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#editStationModal">
                        นัดหมายเข้าบริการ
                    </button>
                    <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#editStationModal">
                        อื่นๆ
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="table-communication">
                            <thead class="thead-light">
                                <tr>
                                    <th>วันที่ เวลา</th>
                                    <th>กิจกรรม</th>
                                    <th>ผู้ติดต่อ</th>
                                    <th>ผู้บันทึก</th>
                                    <th>หมายเหตุ</th>
                                    <th>จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>28/07/2025 14:30</td>
                                    <td>ออกใบเสนอราคา</td>
                                    <td>นายสมชาย ใจดี</td>
                                    <td>คุณวิศรุต</td>
                                    <td>เสนอราคาครั้งแรก</td>
                                    <td><button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#activityModal" onclick="showActivityDetail('ออกใบเสนอราคา', 'เสนอราคาครั้งแรก', '28/07/2025 14:30')">ดูรายละเอียด</button></td>
                                </tr>
                                <tr>
                                    <td>29/07/2025 10:15</td>
                                    <td>ติดต่อลูกค้า</td>
                                    <td>นางสาวสุภาพร</td>
                                    <td>คุณณัฐพล</td>
                                    <td>โทรแจ้งเงื่อนไขการชำระเงิน</td>
                                    <td><button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#activityModal" onclick="showActivityDetail('ติดต่อลูกค้า', 'โทรแจ้งเงื่อนไขการชำระเงิน', '29/07/2025 10:15')">ดูรายละเอียด</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4 full-height ">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">ข้อมูลเอกสาร</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <button id="addDispenserBtn" class="btn btn-primary">➕ เพิ่มตู้จ่าย</button>
                    </div>
                    <div id="dispenser-list" class="accordion" style="margin-top:20px;"></div>

                </div>
            </div>
        </div>

    </div>
</div>
<div class="modal fade" id="activityModal" tabindex="-1" role="dialog" aria-labelledby="activityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">รายละเอียดกิจกรรม</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><strong>วันที่ เวลา:</strong> <span id="detailDateTime"></span></p>
                <p><strong>กิจกรรม:</strong> <span id="detailActivity"></span></p>
                <p><strong>หมายเหตุ:</strong> <span id="detailNote"></span></p>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="addContactModal" tabindex="-1" role="dialog" aria-labelledby="addContactModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="contactForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">เพิ่มผู้ติดต่อใหม่</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label for="contactName">ชื่อผู้ติดต่อ</label>
                        <input type="text" class="form-control" id="contactName" required>
                    </div>

                    <div class="form-group">
                        <label for="contactEmail">อีเมล</label>
                        <input type="email" class="form-control" id="contactEmail" required>
                    </div>

                    <div class="form-group">
                        <label for="contactPhone">เบอร์โทร</label>
                        <input type="tel" class="form-control" id="contactPhone" required>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>


<script>
    $(document).ready(function() {
        let sum = 0;
        const stationId = window.location.pathname.split('/')[2];
        loadDispensers(stationId);
        $('#contactsTable').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            ajax: {
                url: window.APP_URL+'/contacts/getdata',
                data: {
                    station_id: stationId
                }
            },
            columns: [{
                    data: 'ContactName'
                },
                {
                    data: 'ContactEmail',
                    render: function(data) {
                        return `<span class="email-text">${data}</span>`;
                    }
                },
                {
                    data: 'ContactPhone',
                    render: function(data) {
                        return `<a href="tel:${data}">${data}</a>`;
                    }
                },
                {
                    data: 'ContactID',
                    orderable: false,
                    searchable: false,
                    render: function(id) {
                        return `<button class="btn btn-sm btn-danger" onclick="deleteContact(${id})">ลบ</button>`;
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

        $('#table-communication').DataTable({
            ajax: {
                url: window.APP_URL+`/comunicatae/${stationId}`,
                method: 'GET',
                dataSrc: '' // ถ้า API ส่งกลับมาเป็น array ไม่ต้องเจาะ path เช่น `data.items`
            },
            pageLength: 5,
            destroy: true, // สำหรับ reload table เดิม
            columns: [{
                    data: 'created_at',
                    render: function(data) {
                        if (!data) return '-';

                        const date = new Date(data);
                        // ปรับ timezone +7 (เวลาไทย)
                        date.setHours(date.getHours() + 7);

                        return date.toLocaleString('th-TH', {
                            year: 'numeric',
                            month: '2-digit',
                            day: '2-digit',
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                    }
                },
                {
                    data: 'type.ComunicataeName',
                    defaultContent: '-'
                },
                {
                    data: 'contact.ContactName',
                    defaultContent: '-'
                },
                {
                    data: 'user.NameTH',
                    defaultContent: '-'
                },
                {
                    data: 'ComunicataeDetail',
                    defaultContent: '-'
                },
                {
                    data: 'ComunicataeID',
                    orderable: false,
                    searchable: false,
                    render: function(id, type, row) {
                        return `
                    <button class="btn btn-sm btn-primary" 
                        data-toggle="modal" 
                        data-target="#activityModal" 
                        onclick="showActivityDetail('${row.type?.ComunicataeName}', '${row.ComunicataeDetail}', '${row.created_at ?? '-'}')">
                        ดูรายละเอียด
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


    });
    document.getElementById('contactForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const name = document.getElementById('contactName').value;
        const email = document.getElementById('contactEmail').value;
        const phone = document.getElementById('contactPhone').value;
        const stationId = window.location.pathname.split('/')[2];


        fetch("{{ route('contacts.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    ContactName: name,
                    ContactEmail: email,
                    ContactPhone: phone,
                    StationID: stationId,

                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    $('.modal-backdrop').remove();
                    Swal.fire({
                        icon: 'success',
                        title: 'บันทึกสำเร็จ',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    $('#addContactModal').modal('hide');
                    $('#contactsTable').DataTable().ajax.reload(null, false);
                } else {
                    $('.modal-backdrop').remove();
                    Swal.fire({
                        icon: 'danger',
                        title: 'ไม่สามารถเพิ่มผู้ติดต่อได้',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            })
            .catch(err => {
                console.error(err);
                alert("เกิดข้อผิดพลาด");
            });
    });

    function showActivityDetail(activity, note, datetime) {
        document.getElementById('detailDateTime').textContent = datetime;
        document.getElementById('detailActivity').textContent = activity;
        document.getElementById('detailNote').textContent = note;
    }

    function deleteContact(id) {
        Swal.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: "การลบนี้จะไม่สามารถย้อนกลับได้!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ลบเลย!',
            cancelButtonText: 'ยกเลิก',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/contacts/delete/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('ลบแล้ว!', 'ข้อมูลผู้ติดต่อถูกลบเรียบร้อย', 'success');
                            $('#contactsTable').DataTable().ajax.reload(null, false);
                        } else {
                            Swal.fire('เกิดข้อผิดพลาด', data.message || 'ไม่สามารถลบได้', 'error');
                        }
                    })
                    .catch(err => {
                        Swal.fire('ล้มเหลว', 'การเชื่อมต่อผิดพลาด', 'error');
                    });
            }
        });
    }
    $('#btn-add-location-modal').on('click', function() {
        $('#addlocationModal').modal('show');
    });
    $('#btn-show-map').on('click', function() {
        const last = parseFloat($('#ip-last').val().trim());
        const long = parseFloat($('#ip-long').val().trim());

        if (!last || !long || isNaN(last) || isNaN(long)) {
            Swal.fire({
                icon: 'error',
                title: 'กรุณาเพิ่มตำแหน่งสถานี',
                confirmButtonText: 'ตกลง',
            }).then(() => {
                $('#addlocationModal').modal('show');
            });
        } else {
            const iframeUrl = `https://maps.google.com/maps?q=${last},${long}&z=15&output=embed`;
            console.log('iframe URL:', iframeUrl);

            // reset src เพื่อให้ reload ทุกครั้ง
            $('#mapModal iframe').attr('src', '');
            $('#mapModal iframe').attr('src', iframeUrl);

            $('#mapModal').modal('show');
        }
    });

    $('#btn-navigate').on('click', function() {
        const last = $('#ip-last').val();
        const long = $('#ip-long').val();

        if (!last || !long) {
            Swal.fire({
                icon: 'error',
                title: 'กรุณาเพิ่มตำแหน่งสถานี',
                confirmButtonText: 'ตกลง',
                showConfirmButton: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#addlocationModal').modal('show');
                }
            });
        } else {
            const url = `https://www.google.com/maps/dir/?api=1&destination=${last},${long}`;
            window.open(url, '_blank');
        }
    });
    $('#btn-qt').on('click', function(e) {
        const stationid = $('#stationid').val();
        const last = $('#ip-last').val();
        const long = $('#ip-long').val();
            loadPromotionList();
        if (!last || !long) {
            Swal.fire({
                icon: 'error',
                title: 'กรุณาเพิ่มตำแหน่งสถานี',
                confirmButtonText: 'ตกลง',
            }).then(() => {
                $('#addlocationModal').modal('show');
            });
            return;
        }

        Swal.fire({
            title: 'กำลังโหลดข้อมูล...',
            html: 'กรุณารอสักครู่',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: window.APP_URL+`/stations/countNozzles/${stationid}`,
            type: 'GET',
            success: function(response) {
                let nozzleCount = response;

                if (!nozzleCount || nozzleCount == 0) {
                    Swal.fire({
                        title: 'กรุณากรอกจำนวนหัวจ่าย',
                        input: 'number',
                        inputAttributes: {
                            min: 1
                        },
                        inputPlaceholder: 'เช่น 4',
                        confirmButtonText: 'ตกลง',
                        showCancelButton: true,
                        cancelButtonText: 'ยกเลิก',
                        preConfirm: (value) => {
                            if (!value || value <= 0) {
                                Swal.showValidationMessage('กรุณากรอกตัวเลขมากกว่า 0');
                            }
                            return value;
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            nozzleCount = parseInt(result.value);
                            proceedToCharge(nozzleCount, last, long);
                            Swal.fire({
                                title: 'กำลังโหลดข้อมูล...',
                                html: 'กรุณารอสักครู่',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                        } else {
                            Swal.close(); // ปิด loading ถ้ากดยกเลิก
                        }
                    });
                } else {
                    proceedToCharge(nozzleCount, last, long);
                    Swal.fire({
                        title: 'กำลังโหลดข้อมูล...',
                        html: 'กรุณารอสักครู่',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                }
            },
            error: function(xhr) {
                Swal.fire('ผิดพลาด', xhr.responseText, 'error');
            }
        });
    });

    function proceedToCharge(nozzleCount, last, long) {
        let sum = 0;

        $.ajax({
            url: window.APP_URL+`/calculate-charge/${nozzleCount}`,
            type: 'GET',
            success: function(res) {
                let html = `
                <tr>
                    <td><B>1.ค่าบริการปรับมาตรวัดพร้อมตีตราหัวจ่ายน้ำมัน จำนวน ${res.nozzle_count} หัวจ่าย </B>`;
                res.breakdown.forEach(nz => {
                    html += `<br>&emsp;<i>${nz.range} จำนวน ${nz.quantity} หัวจ่าย หัวจ่ายละ  ${nz.rate} เป็นเงิน  ${nz.amount} บาท</i>`;
                });
                html += `</td>
                    <td class="text-center">1</td>
                    <td class="text-end">${res.total_charge}</td>
                    <td class="text-end">${res.total_charge}</td>
                    <input type="hidden" id="ip-qt-totolcharge" value="${res.total_charge}">
                </tr>`;
                let totalCharge = parseFloat((res.total_charge + '').replace(/,/g, '')) || 0;
                sum += totalCharge;
                $('#qt-service-table tbody').html(html);

                $.ajax({
                    url: window.APP_URL+`/calculate-distance/${last}/${long}`,
                    type: 'GET',
                    success: function(res) {
                        const distance_km = res.distance_km;
                        $.ajax({
                            url: window.APP_URL+`/calculate-travel/${distance_km}`,
                            type: 'GET',
                            success: function(res) {
                                let html = `
                                <tr>
                                    <td>2.ค่าเดินทาง ระยะทาง  ${res.distance} กิโลเมตร</td>`;
                                res.details.forEach(nz => {
                                    if (nz.type === 'flat') {
                                        html += `<br>&emsp;<i>ระยะทาง ${nz.range} เหมาจ่าย ${nz.price} บาท</i>`;
                                    } else {
                                        html += `<br>&emsp;<i>ระยะทาง ${nz.range} กม.ละ ${nz.price_km} บาท  เป็นเงิน  ${nz.price} บาท</i>`;
                                    }
                                });

                                html += `</td>
                                <td class="text-center">1</td>
                                <td class="text-end">${res.total_price}</td>
                                <td class="text-end">${res.total_price}</td>
                                 <input type="hidden" id="ip-qt-totoltarvel" value="${res.total_price}">

                            </tr>`;
                                let totalTravel = parseFloat((res.total_price + '').replace(/,/g, '')) || 0;
                                sum += totalTravel;
                                $('#qt-service-table tbody').append(html);
                                html = `<tr class="table-info total-row">
                                    <td colspan="3" class="text-end"><strong>ราคารวมทั้งหมด:</strong></td>
                                    <td class="text-end"><strong>${sum.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</strong></td>
                            </tr>
                            <input type="hidden" id="ip-qt-totalprice" value="${sum}">`;


                                $('#qt-service-table tbody').append(html);

                                // รีเซ็ตฟอร์มส่วนลดหลังจากมีข้อมูลครบแล้ว
                                initializeDiscountForm();

                                $('#quotationModal').modal('show');
                                Swal.close();
                            },
                            error: function() {
                                Swal.fire('ผิดพลาด', 'โหลดข้อมูลค่าเดินทางล้มเหลว', 'error');
                            }
                        });
                    },
                    error: function() {
                        Swal.fire('ผิดพลาด', 'โหลดข้อมูลระยะทางล้มเหลว', 'error');
                    }
                });
            },
            error: function() {
                Swal.fire('ผิดพลาด', 'โหลดข้อมูลค่าบริการล้มเหลว', 'error');
            }
        });
    }
    function initializeDiscountForm() {
        $('#ip-qt-discounttype').val('');
        $('#ip-qt-discountunit').val('baht');
        $('#ip-qt-discount').val(0);
        
        // อัปเดตราคารวมที่แสดง
        const totalPrice = parseFloat($('#ip-qt-totalprice').val()) || 0;
        $('#qt-total-price').text(totalPrice.toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }));
    }
    $('#btn-save-linkgooglemap').on('click', function(e) {

        e.preventDefault();

        const station_id = $('#ip-addlink-stationid').val();
        const map_url = $('#ip-linkgooglemap').val();
        console.log(station_id);
        $.ajax({
            url: window.APP_URL+`/stations/updatelatlong`,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                map_url: map_url,
                station_id: station_id
            },
            success: function(response) {
                Swal.fire('สำเร็จ!', response.message, 'success');
                $('#ip-last').val(response.data.last);
                $('#ip-long').val(response.data.long);
                $('#addlocationModal').modal('hide');
            },
            error: function(xhr) {
                let msg = xhr.responseJSON?.error ?? 'เกิดข้อผิดพลาด';
                Swal.fire('ล้มเหลว', msg, 'error');
                $('#addlocationModal').modal('hide');

            }
        });

    });
    $('#mapModal').on('shown.bs.modal', function(e) {
        const button = $(e.relatedTarget); // ปุ่มที่กด
        const lat = parseFloat(button.data('lat')) || 13.7563;
        const lng = parseFloat(button.data('lng')) || 100.5018;
        console.log(lat);
        const map = new google.maps.Map(document.getElementById('map'), {
            zoom: 14,
            center: {
                lat,
                lng
            },
        });

        new google.maps.Marker({
            position: {
                lat,
                lng
            },
            map: map,
        });

        mapInitialized = true;

    });

    function loadDispensers(stationId) {
        $.ajax({
            url: window.APP_URL+`/stations/${stationId}/dispensers/data`,
            method: 'GET',
            success: function(dispensers) {
                renderDispenserAccordion(stationId, dispensers.data);
            },
            error: function() {
                $('#dispenser-list').html('<div class="text-danger">โหลดข้อมูลไม่สำเร็จ</div>');
            }
        });
    }

    function renderDispenserAccordion(stationId, dispensers) {
        let html = '';

        dispensers.forEach((dispenser, index) => {
            const collapseId = `collapse${index}`;
            html += `
            <div class="card">
                <div class="card-header" id="heading${index}">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button"
                                data-toggle="collapse" data-target="#${collapseId}" aria-expanded="false"
                                aria-controls="${collapseId}" onclick="loadNozzles( ${dispenser.DispenserID}, '${collapseId}')">
                            ตู้จ่าย ${index + 1} - ยี่ห้อ ${dispenser.brand.BrandName} - รุ่น ${dispenser.Model}
                        </button>
                    </h2>
                </div>

                <div id="${collapseId}" class="collapse" aria-labelledby="heading${index}" data-parent="#dispenser-list">
                    <div class="card-body">
                        <div id="nozzle-${collapseId}">⏳ กำลังโหลดข้อมูลหัวจ่าย...</div>
                        <button class="btn btn-outline-success btn-sm mt-2" onclick="showAddNozzleForm(${stationId},${dispenser.DispenserID}, '${collapseId}')">➕ เพิ่มหัวจ่าย</button>
                    <div id="add-nozzle-form-${collapseId}" class="mt-2"></div>
                        </div>
                </div>
            </div>
        `;
        });

        $('#dispenser-list').html(html);
    }

    function loadNozzles(dispenserId, containerId) {
        $.ajax({
            url: window.APP_URL+`/stations/dispensers/${dispenserId}/nozzle/data`,
            method: 'GET',
            success: function(res) {
                let html = `
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>หัวที่</th>
                            <th>ชนิดน้ำมัน</th>
                            <th>FlowRate</th>
                            <th>สอบเทียบล่าสุด</th>
                        </tr>
                    </thead>
                    <tbody>`;

                res.data.forEach(nz => {
                    console.log(nz);
                    html += `
                    <tr>
                        <td>${nz.NozzleNumber}</td>
                        <td>${nz.fule_type.FuleTypeName ?? '-'}</td>
                        <td>${nz.FlowRate ?? '-'}</td>
                        <td>${nz.LastCalibationDate ?? '-'}</td>
                    </tr>`;
                });

                html += `</tbody></table>`;
                $(`#nozzle-${containerId}`).html(html);
                console.log($(`#nozzle-${containerId}`).html(html));
            },
            error: () => {
                $(`#nozzle-${containerId}`).html('<div class="text-danger">โหลดข้อมูลหัวจ่ายไม่สำเร็จ</div>');
            }
        });
    }
    $('#addComForm').submit(function(e) {
        e.preventDefault();

        const formData = $(this).serialize();

        $.ajax({
            url: window.APP_URL+'/comunicatae',
            method: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function() {
                $('#addComModal').modal('hide');
                $('#table-communication').DataTable().ajax.reload(null, false);
                $('.modal-backdrop').remove();
                Swal.fire({
                    icon: 'success',
                    title: 'บันทึกสำเร็จ',
                    showConfirmButton: false,
                    timer: 1500
                });
            },
            error: function() {
                Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถบันทึกได้', 'error');
            }
        });
    });

    $('#addDispenserBtn').on('click', function() {
        const html = `
        <div class="card border-primary mb-3">
            <div class="card-header">เพิ่มตู้จ่ายใหม่</div>
            <div class="card-body">
                <form id="dispenser-form">
                    <div class="form-group">
                        <label>Model</label>
                        <input type="text" class="form-control" name="Model" required>
                    </div>
                    <button type="submit" class="btn btn-success btn-sm">💾 บันทึกตู้จ่าย</button>
                </form>
            </div>
        </div>`;
        $('#dispenser-list').prepend(html);
    });

    // บันทึกข้อมูล
    $(document).on('submit', '#dispenser-form', function(e) {
        e.preventDefault();
        const data = $(this).serialize();
        $.post(window.APP_URL+'/api/dispensers', data, function() {
            alert('เพิ่มตู้จ่ายสำเร็จ');
            loadDispensers(1); // โหลดใหม่
        });
    });

    function showAddNozzleForm(stationId, dispenserId, collapseId) {
        $.get(window.APP_URL+'/api/fuletype/list', function(fuelTypes) {
            let fuelOptions = fuelTypes.map(f => `<option value="${f.FuleTypeID}">${f.FuleTypeName}</option>`).join('');

            const formHtml = `
        <form id="nozzle-form-${dispenserId}">
            <input type="hidden" name="stationId" value="${stationId}">
            <input type="hidden" name="DispenserID" value="${dispenserId}">
            <div class="form-group">
                <label>เลขหัวจ่าย</label>
                <input type="text" name="NozzleNumber" class="form-control" required  placeholder="x-xxxxxx-x-xxxx-xxxxxx-xx">
                <small class="form-text text-muted">รูปแบบ: x-xxxxxx-x-xxxx-xxxxxx-xx</small>
            </div>
            <div class="form-group">
                <label>ประเภทน้ำมัน</label>
                <select name="FuleTypeID" class="form-control" required>
                    <option value="">-- เลือกประเภทน้ำมัน --</option>
                    ${fuelOptions}
                </select>
            </div>
            <div class="form-group">
                <label>Flow Rate</label>
                <input type="number" name="FlowRate" class="form-control">
            </div>
            <div class="form-group">
                <label>MMQ (L)</label>
                <input type="number" name="MMQ" class="form-control">
            </div>
            <div class="form-group">
                <label>Q max (L/min)</label>
                <input type="number" name="Qmax" class="form-control">
            </div>
            <div class="form-group">
                <label>Q min (L/min)</label>
                <input type="number" name="Qmin" class="form-control">
            </div>
            <div class="form-group">
                <label>S/N</label>
                <input type="text" name="SN" class="form-control">
            </div>
            <div class="form-group">
                <label>วันสอบเทียบล่าสุด (Last Calibration Date)</label>
                <input type="date" name="LastCalibrationDate" class="form-control">
            </div>
            <button type="submit" class="btn btn-sm btn-primary">💾 บันทึกหัวจ่าย</button>
        </form>`;

            $(`#add-nozzle-form-${collapseId}`).html(formHtml);
            console.log(collapseId);
            $(`#nozzle-form-${dispenserId}`).on('submit', function(e) {
                e.preventDefault();
                const data = $(this).serialize(); // DispenserID รวมมาแล้วใน hidden
                $.post(window.APP_URL+`/api/nozzles`, data, function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'บันทึกสำเร็จ',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    loadNozzles(dispenserId, collapseId);
                    $(`#add-nozzle-form-${collapseId}`).html(null); // รีเฟรชรายการหัวจ่าย
                });
            });
        });
    }
</script>
<script
    src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&callback=initMap"
    async defer></script>

<script>
    function initMap() {
        console.log("Google Maps Ready!");
        // init ตำแหน่ง map ที่นี่ได้เลย (หรือซ่อนไว้รอ modal เปิดค่อยแสดง)
    }
</script>
<script>
    document.getElementById('copyFromStationBtn').addEventListener('click', function() {
        const station = @json($station);
        console.log(station);
        document.querySelector('input[name="CustomerName"]').value = station.StationName;
        document.querySelector('input[name="TaxID"]').value = station.TaxID;
        document.querySelector('textarea[name="Address"]').value = station.Address;
        document.querySelector('input[name="Postcode"]').value = station.Postcode;
        document.querySelector('input[name="Telphone"]').value = station.Telphone;
        document.querySelector('input[name="Phone"]').value = station.Phone;
        document.querySelector('input[name="Email"]').value = station.Email;
        $('#id-province').val(station.province).trigger('change');

        setTimeout(() => {
            $('#ip-district').val(station.district).trigger('change');
        }, 500);

        setTimeout(() => {
            $('#ip-subdistrict').val(station.subdistrict).trigger('change');
        }, 1000);
        $('#addCustomerModal').modal('show');
    });
</script>
<script src="{{ asset('js/location-select.js') }}"></script>
<script>
    setupLocationSelect('#id-province', '#ip-district', '#ip-subdistrict', '#ip-postcode');
</script>
@endsection