<!-- 🔶 MODAL: ใบเสนอราคา -->
<div class="modal fade" id="quotationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header">
                <h5 class="modal-title">ใบเสนอราคา</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                <!-- 🔸 ข้อมูลลูกค้า -->
                <h6 class="mb-3">ข้อมูลลูกค้า</h6>

                <p><strong>ชื่อลูกค้า:</strong> <span id="qt-cus-name">{{ $customer->CustomerName ?? $station->StationName }}</span></p>
                <p><strong>เลขประจำตัวผู้เสียภาษี:</strong> <span id="qt-cus-taxid">{{ $customer->TaxID ?? $station->TaxID }}</span></p>
                <p><strong>ที่อยู่:</strong>
                    <span id="qt-cus-address">
                        {{ $customer->Address ?? $station->Address }}
                        {{ $customer->subdistrict->NameInThai ?? $station->subdistrict->NameInThai ?? '-' }}
                        {{ $customer->district->NameInThai ?? $station->district->NameInThai  ?? '-' }}
                        {{ $customer->province->NameInThai ?? $station->province->NameInThai  ?? '-' }}
                        {{ $customer->subdistrict->ZipCode ?? $station->subdistrict->ZipCode   ?? '-'}}
                    </span>
                </p>
                <input type="hidden" id="ip-qt-customerid" value="{{ $customer->CustomerID??0 }}">
                <input type="hidden" id="ip-qt-detail">

                <hr>

                <!-- 🔸 รายละเอียดการให้บริการ -->
                <h6 class="mb-3">รายละเอียดการให้บริการ</h6>
                <table class="table table-bordered align-middle" id="qt-service-table">
                    <thead class="table-light">
                        <tr>
                            <th>รายการ</th>
                            <th class="text-center">จำนวน</th>
                            <th class="text-end">ราคาต่อหน่วย (บาท)</th>
                            <th class="text-end">รวม (บาท)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- เติมข้อมูลด้วย JS -->
                    </tbody>
                    <tfoot>

                    </tfoot>
                </table>

                <!-- 🔸 หมายเหตุ -->
                <p class="text-muted mt-4">** หมายเหตุ: ราคาอาจเปลี่ยนแปลงได้ตามจำนวนหัวจ่ายจริงและระยะทาง **</p>

                <!-- 🔸 ส่วนลด -->
                <div class="form-group row">
                    <label for="ip-qt-discounttype" class="col-sm-2 col-form-label">ประเภทส่วนลด</label>
                    <div class="col-sm-3">
                        <select class="form-control" id="ip-qt-discounttype">
                            <option value="">-- โปรดเลือก --</option>
                            <option value="service">ลดเฉพาะค่าบริการ</option>
                            <option value="travel">ลดเฉพาะค่าเดินทาง</option>
                            <option value="total">ลดท้ายบิล</option>
                        </select>
                    </div>

                    <label for="ip-qt-discountunit" class="col-sm-1 col-form-label">ลดแบบ</label>
                    <div class="col-sm-2">
                        <select class="form-control" id="ip-qt-discountunit">
                            <option value="baht">บาท</option>
                            <option value="percent">%</option>
                        </select>
                    </div>

                    <label for="ip-qt-discount" class="col-sm-1 col-form-label">ส่วนลด</label>
                    <div class="col-sm-2">
                        <input type="number" class="form-control" id="ip-qt-discount" value="0" />
                    </div>

                    <div class="col-sm-1">
                        <button class="btn btn-primary" id="qt-update-btn">อัปเดตราคา</button>
                    </div>
                </div>

                <!-- 🔸 VAT -->
                <div class="form-group row">
                    <label for="ip-qt-vat-enabled" class="col-sm-2 col-form-label">VAT</label>
                    <div class="col-sm-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="ip-qt-vat-enabled" checked>
                            <label class="form-check-label" for="ip-qt-vat-enabled">
                                ไม่รวม VAT 7%
                            </label>
                        </div>
                    </div>
                </div>

                <!-- 🔸 ราคารวม -->
                <div class="text-end mt-3">
                    <div class="row">
                        <div class="col-md-6"></div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <span>ยอดรวมก่อน VAT:</span>
                                        <span id="qt-subtotal-price">0.00 บาท</span>
                                    </div>
                                    <div class="d-flex justify-content-between" id="vat-row">
                                        <span>VAT 7%:</span>
                                        <span id="qt-vat-amount">0.00 บาท</span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <h5><strong>รวมทั้งหมด:</strong></h5>
                                        <h5><strong id="qt-total-price">0.00 บาท</strong></h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <h6 class="mb-2">หมายเหตุ/ข้อเสนอพิเศษ</h6>
                        <textarea class="form-control" id="note" rows="6" placeholder="กรอกหมายเหตุเพิ่มเติม..."></textarea>
                        <small class="form-text text-muted">หมายเหตุและโปรโมชั่นที่เลือกจะแสดงที่นี่</small>
                    </div>
                    <div class="col-md-6">
                        <div id="promotionlist">
                            <!-- โปรโมชั่นจะถูกโหลดที่นี่ -->
                            <div class="text-center">
                                <div class="spinner-border spinner-border-sm" role="status">
                                    <span class="sr-only">กำลังโหลด...</span>
                                </div>
                                <p class="mt-2">กำลังโหลดโปรโมชั่น...</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <input type="hidden" id="ip-qt-detail" name="detail_json">
            <input type="hidden" id="ip-qt-service-total" name="service_total">
            <input type="hidden" id="ip-qt-travel-total" name="travel_total">
            <input type="hidden" id="ip-qt-subtotal" name="subtotal">
            <input type="hidden" id="ip-qt-discounttype-hidden" name="discount_type">
            <input type="hidden" id="ip-qt-discountunit-hidden" name="discount_unit">
            <input type="hidden" id="ip-qt-discount-hidden" name="discount_value">
            <input type="hidden" id="ip-qt-vat-enabled-hidden" name="vat_enabled">
            <input type="hidden" id="ip-qt-vat-amount-hidden" name="vat_amount">
            <input type="hidden" id="ip-qt-grand-total" name="final_total">
            <!-- Footer -->

            <div class="modal-footer">
                <button type="button" class="btn btn-warning" id="qt-reset-btn">รีเซ็ต</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                <button type="button" class="btn btn-success" id="qt-print-btn">บันทึกและพิมพ์</button>
            </div>

        </div>
    </div>
</div>

<script>
    $('#qt-print-btn').on('click', function() {
        // เก็บรายการในตาราง
        document.getElementById('qt-update-btn').click();
        const items = [];
        $('#qt-service-table tbody tr').each(function() {
            const tds = $(this).find('td');
            if (tds.length >= 4 && !$(this).hasClass('discount-row') && !$(this).hasClass('total-row') && !$(this).hasClass('vat-row')) {
                items.push({
                    name: $(tds[0]).text().trim(),
                    description: $(tds[0]).text().trim(),
                    item_code: '1010600023',
                    qty: parseFloat($(tds[1]).text().replace(/,/g, '')) || 1,
                    unit: 'Set',
                    unit_price: parseFloat($(tds[2]).text().replace(/,/g, '')) || 0,
                    discount_percent: 0,
                    total_price: parseFloat($(tds[3]).text().replace(/,/g, '')) || 0,
                    detail: '',
                    remark: ''
                });
            }
        });

        // อัปเดต hidden field สำหรับ detail
        $('#ip-qt-detail').val(JSON.stringify(items));

        // เตรียมข้อมูลสำหรับส่ง
        const quotationData = {
            station_id: $('#stationid').val(),
            customer_id: $('#ip-qt-customerid').val(),
            items: items,
            service_total: parseFloat($('#ip-qt-service-total').val()) || 0,
            travel_total: parseFloat($('#ip-qt-travel-total').val()) || 0,
            subtotal: parseFloat($('#ip-qt-subtotal').val()) || 0,
            discount_type: $('#ip-qt-discounttype').val(),
            discount_unit: $('#ip-qt-discountunit').val(),
            discount_value: parseFloat($('#ip-qt-discount').val()) || 0,
            vat_enabled: $('#ip-qt-vat-enabled').is(':checked'),
            vat_amount: parseFloat($('#ip-qt-vat-amount-hidden').val()) || 0,
            final_total: parseFloat($('#ip-qt-grand-total').val()) || 0,
            note: $('#note').val()
        };

        console.log('Quotation Data:', quotationData);

        // ส่งข้อมูลไปยัง server
        saveQuotation(quotationData);
    });

    function saveQuotation(data) {
        Swal.fire({
            title: 'กำลังบันทึกใบเสนอราคา...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: window.APP_URL+'/quotations/store',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                ...data
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'บันทึกใบเสนอราคาสำเร็จ',
                    html: `
                    <p><strong>เลขที่เอกสาร:</strong> ${response.doc_no}</p>
                    <p><strong>เลขที่อ้างอิง:</strong> ${response.quotation_no}</p>
                `,
                    showConfirmButton: true,
                    confirmButtonText: 'ตกลง'
                }).then(() => {
                    $('#quotationModal').modal('hide');

                    if (typeof $('#table-communication').DataTable === 'function') {
                        $('#table-communication').DataTable().ajax.reload(null, false);
                    }

                    if (typeof $('#quotations-table').DataTable === 'function') {
                        $('#quotations-table').DataTable().ajax.reload(null, false);
                    }

                    if (response.pdf_url) {
                        window.open(response.pdf_url, '_blank');
                    }
                });
            },
            error: function(xhr) {
                let errorMsg = 'เกิดข้อผิดพลาดในการบันทึก';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: errorMsg
                });
            }
        });
    }

    function copyDocNo(docNo) {
        navigator.clipboard.writeText(docNo).then(function() {
            Swal.fire({
                icon: 'success',
                title: 'คัดลอกสำเร็จ',
                text: 'คัดลอกเลขที่เอกสาร ' + docNo + ' แล้ว',
                timer: 2000,
                showConfirmButton: false
            });
        });
    }

    $('#qt-reset-btn').on('click', function() {
        resetDiscountForm();
    });

    // เพิ่ม Event Listener สำหรับ VAT checkbox
    $('#ip-qt-vat-enabled').on('change', function() {
        $('#qt-update-btn').click();
    });

    function resetDiscountForm() {
        $('#ip-qt-discounttype').val('');
        $('#ip-qt-discountunit').val('baht');
        $('#ip-qt-discount').val(0);
        $('#ip-qt-vat-enabled').prop('checked', true);

        // รีเซ็ตโปรโมชั่นด้วย
        resetPromotions();

        // ลบแถวส่วนลดและอัปเดตราคา
        $('#qt-service-table tbody tr.discount-row, #qt-service-table tbody tr.total-row, #qt-service-table tbody tr.vat-row').remove();

        const originalTotal = parseFloat($('#ip-qt-totalprice').val().replace(/,/g, '')) || 0;
        const vatAmount = originalTotal * 0.07;
        const finalTotal = originalTotal + vatAmount;

        const html = `
        <tr class="table-info total-row">
            <td colspan="3" class="text-end"><strong>ราคารวมก่อน VAT:</strong></td>
            <td class="text-end"><strong>${originalTotal.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</strong></td>
        </tr>
        <tr class="table-warning vat-row">
            <td colspan="3" class="text-end"><strong>VAT 7%:</strong></td>
            <td class="text-end"><strong>${vatAmount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</strong></td>
        </tr>
        <tr class="table-success total-row">
            <td colspan="3" class="text-end"><strong>ราคารวมทั้งหมด:</strong></td>
            <td class="text-end"><strong>${finalTotal.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</strong></td>
        </tr>`;

        $('#qt-service-table tbody').append(html);
        
        // อัปเดตการแสดงผล
        $('#qt-subtotal-price').text(originalTotal.toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }) + ' บาท');
        
        $('#qt-vat-amount').text(vatAmount.toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }) + ' บาท');
        
        $('#qt-total-price').text(finalTotal.toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }) + ' บาท');
    }

    $('#qt-update-btn').on('click', function() {
        let totalCharge = parseFloat($('#ip-qt-totolcharge').val().replace(/,/g, '')) || 0;
        let totalTravel = parseFloat($('#ip-qt-totoltarvel').val().replace(/,/g, '')) || 0;
        let totalPrice = parseFloat($('#ip-qt-totalprice').val().replace(/,/g, '')) || 0;

        const discountType = $('#ip-qt-discounttype').val();
        const discountUnit = $('#ip-qt-discountunit').val();
        const discountValue = parseFloat($('#ip-qt-discount').val()) || 0;
        const vatEnabled = $('#ip-qt-vat-enabled').is(':checked');

        let baseAmount = 0;
        let textDiscount = '';

        // กำหนด baseAmount และ textDiscount
        switch (discountType) {
            case 'service':
                baseAmount = totalCharge;
                textDiscount = "ค่าบริการปรับมาตรวัดพร้อมตีตราหัวจ่ายน้ำมัน";
                break;
            case 'travel':
                baseAmount = totalTravel;
                textDiscount = "ค่าเดินทาง";
                break;
            case 'total':
                baseAmount = totalPrice;
                textDiscount = "ท้ายบิล";
                break;
            default:
                baseAmount = 0;
        }

        // คำนวณส่วนลด
        let discountAmount = 0;
        if (discountType && discountValue > 0) {
            if (discountUnit === 'percent') {
                discountAmount = (discountValue / 100) * baseAmount;
            } else {
                discountAmount = discountValue;
            }
            discountAmount = Math.min(discountAmount, baseAmount);
        }

        // คำนวณราคาหลังหักส่วนลด
        const subtotalAfterDiscount = totalPrice - discountAmount;

        // คำนวณ VAT
        let vatAmount = 0;
        if (vatEnabled) {
            vatAmount = subtotalAfterDiscount * 0.07;
        }

        // คำนวณราคาสุทธิ
        const finalTotal = subtotalAfterDiscount + vatAmount;

        // อัปเดต hidden fields
        $('#ip-qt-service-total').val(totalCharge);
        $('#ip-qt-travel-total').val(totalTravel);
        $('#ip-qt-subtotal').val(subtotalAfterDiscount);
        $('#ip-qt-discounttype-hidden').val(discountType);
        $('#ip-qt-discountunit-hidden').val(discountUnit);
        $('#ip-qt-discount-hidden').val(discountValue);
        $('#ip-qt-vat-enabled-hidden').val(vatEnabled);
        $('#ip-qt-vat-amount-hidden').val(vatAmount);
        $('#ip-qt-grand-total').val(finalTotal);

        // อัปเดตตาราง
        updateQuotationTable(discountAmount, subtotalAfterDiscount, vatAmount, finalTotal, textDiscount, vatEnabled);

        // อัปเดตการแสดงผลด้านข้าง
        $('#qt-subtotal-price').text(subtotalAfterDiscount.toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }) + ' บาท');
        
        if (vatEnabled) {
            $('#qt-vat-amount').text(vatAmount.toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) + ' บาท');
            $('#vat-row').show();
        } else {
            $('#qt-vat-amount').text('0.00 บาท');
            $('#vat-row').hide();
        }
        
        $('#qt-total-price').text(finalTotal.toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }) + ' บาท');
    });

    function updateQuotationTable(discountAmount, subtotalAfterDiscount, vatAmount, finalTotal, textDiscount, vatEnabled) {
        // ลบแถวส่วนลด, VAT และราคาสุทธิเก่า
        $('#qt-service-table tbody tr.discount-row, #qt-service-table tbody tr.total-row, #qt-service-table tbody tr.vat-row').remove();

        let html = '';

        // แสดงส่วนลด (ถ้ามี)
        if (discountAmount > 0) {
            html += `
            <tr class="table-danger discount-row">
                <td colspan="3" class="text-end"><strong>ส่วนลด${textDiscount}:</strong></td>
                <td class="text-end"><strong>-${discountAmount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</strong></td>
            </tr>`;
        }

        // แสดงยอดรวมก่อน VAT
        html += `
        <tr class="table-info total-row">
            <td colspan="3" class="text-end"><strong>ราคารวมก่อน VAT:</strong></td>
            <td class="text-end"><strong>${subtotalAfterDiscount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</strong></td>
        </tr>`;

        // แสดง VAT (ถ้าเปิดใช้งาน)
        if (vatEnabled) {
            html += `
            <tr class="table-warning vat-row">
                <td colspan="3" class="text-end"><strong>VAT 7%:</strong></td>
                <td class="text-end"><strong>${vatAmount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</strong></td>
            </tr>`;
        }

        // แสดงราคาสุทธิ
        html += `
        <tr class="table-success total-row">
            <td colspan="3" class="text-end"><strong>ราคาสุทธิ:</strong></td>
            <td class="text-end"><strong>${finalTotal.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</strong></td>
        </tr>`;

        $('#qt-service-table tbody').append(html);
    }

    // ฟังก์ชันสำหรับโหลดรายการ Promotion
    function loadPromotionList() {
        $.ajax({
            url: window.APP_URL+'/api/promotions',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                renderPromotionList(response);
            },
            error: function(xhr, status, error) {
                console.error('Error loading promotions:', error);
                $('#promotionlist').html('<div class="alert alert-danger">ไม่สามารถโหลดข้อมูลโปรโมชั่นได้</div>');
            }
        });
    }

    // ฟังก์ชันสำหรับจัดการ Event ของ checkbox
    function bindPromotionCheckboxEvents() {
        $('.promotion-checkbox').on('change', function() {
            updateNoteTextarea();
        });
    }

    // ฟังก์ชันสำหรับอัปเดต textarea note
    function updateNoteTextarea() {
        const selectedPromotions = [];

        $('.promotion-checkbox:checked').each(function() {
            const promotionText = $(this).val().trim();
            if (promotionText) {
                selectedPromotions.push('- ' + promotionText);
            }
        });

        const currentNote = $('#note').val();
        const lines = currentNote.split('\n');
        const nonPromotionLines = lines.filter(line => !line.trim().startsWith('- '));

        let finalNote = '';

        if (nonPromotionLines.length > 0) {
            finalNote = nonPromotionLines.join('\n').trim();
            if (selectedPromotions.length > 0) {
                finalNote += '\n\n' + selectedPromotions.join('\n');
            }
        } else {
            finalNote = selectedPromotions.join('\n');
        }

        $('#note').val(finalNote);
    }

    // ฟังก์ชันสำหรับล้าง checkbox ทั้งหมด
    function clearAllPromotions() {
        $('.promotion-checkbox').prop('checked', false);
        updateNoteTextarea();
    }

    // ฟังก์ชันสำหรับเลือก checkbox ทั้งหมด
    function selectAllPromotions() {
        $('.promotion-checkbox').prop('checked', true);
        updateNoteTextarea();
    }

    $('#quotationModal').on('shown.bs.modal', function() {
        loadPromotionList();
        console.log(5555)
    });

    // เพิ่มปุ่มควบคุมโปรโมชั่น (ไม่บังคับ)
    $(document).ready(function() {
        $(document).on('click', '.select-all-promotions', function() {
            selectAllPromotions();
        });

        $(document).on('click', '.clear-all-promotions', function() {
            clearAllPromotions();
        });
    });

    function renderPromotionList(promotions) {
        let html = '<h6 class="mb-3">โปรโมชั่น/ข้อเสนอพิเศษ</h6>';

        if (promotions && promotions.length > 0) {
            html += `
            <div class="mb-2">
                <button type="button" class="btn btn-sm btn-outline-primary select-all-promotions">เลือกทั้งหมด</button>
                <button type="button" class="btn btn-sm btn-outline-secondary clear-all-promotions">ล้างการเลือก</button>
            </div>
        `;

            html += '<div class="promotion-list" style="max-height: 200px; overflow-y: auto;">';

            promotions.forEach(function(promotion, index) {
                html += `
                <div class="form-check mb-2">
                    <input class="form-check-input promotion-checkbox" 
                           type="checkbox" 
                           value="${promotion.detail || ''}" 
                           id="promotion_${index}">
                    <label class="form-check-label" for="promotion_${index}">
                        ${promotion.detail || 'ไม่มีรายละเอียด'}
                    </label>
                </div>
            `;
            });

            html += '</div>';
        } else {
            html += '<div class="alert alert-info">ไม่มีโปรโมชั่นในขณะนี้</div>';
        }

        $('#promotionlist').html(html);
        bindPromotionCheckboxEvents();
    }

    // ฟังก์ชันสำหรับรีเซ็ตโปรโมชั่นเมื่อปิด modal หรือรีเซ็ต
    function resetPromotions() {
        $('.promotion-checkbox').prop('checked', false);
        const currentNote = $('#note').val();
        const lines = currentNote.split('\n');
        const nonPromotionLines = lines.filter(line => !line.trim().startsWith('- '));
        $('#note').val(nonPromotionLines.join('\n').trim());
    }

    // เพิ่มการรีเซ็ตโปรโมชั่นใน resetDiscountForm ใหม่
    function resetDiscountForm() {
        $('#ip-qt-discounttype').val('');
        $('#ip-qt-discountunit').val('baht');
        $('#ip-qt-discount').val(0);
        $('#ip-qt-vat-enabled').prop('checked', true);

        // รีเซ็ตโปรโมชั่นด้วย
        resetPromotions();

        // ลบแถวส่วนลดและอัปเดตราคา
        $('#qt-service-table tbody tr.discount-row, #qt-service-table tbody tr.total-row, #qt-service-table tbody tr.vat-row').remove();

        const originalTotal = parseFloat($('#ip-qt-totalprice').val().replace(/,/g, '')) || 0;
        const vatAmount = originalTotal * 0.07;
        const finalTotal = originalTotal + vatAmount;

        const html = `
        <tr class="table-info total-row">
            <td colspan="3" class="text-end"><strong>ราคารวมก่อน VAT:</strong></td>
            <td class="text-end"><strong>${originalTotal.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</strong></td>
        </tr>
        <tr class="table-warning vat-row">
            <td colspan="3" class="text-end"><strong>VAT 7%:</strong></td>
            <td class="text-end"><strong>${vatAmount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</strong></td>
        </tr>
        <tr class="table-success total-row">
            <td colspan="3" class="text-end"><strong>ราคารวมทั้งหมด:</strong></td>
            <td class="text-end"><strong>${finalTotal.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</strong></td>
        </tr>`;

        $('#qt-service-table tbody').append(html);
        
        // อัปเดตการแสดงผล
        $('#qt-subtotal-price').text(originalTotal.toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }) + ' บาท');
        
        $('#qt-vat-amount').text(vatAmount.toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }) + ' บาท');
        
        $('#qt-total-price').text(finalTotal.toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }) + ' บาท');
    }
</script>