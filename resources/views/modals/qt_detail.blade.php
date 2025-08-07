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
                        {{ $customer->subdistrict->NameInThai ?? $station->subdistrict->NameInThai }}
                        {{ $customer->district->NameInThai ?? $station->district->NameInThai }}
                        {{ $customer->province->NameInThai ?? $station->province->NameInThai }}
                        {{ $customer->subdistrict->ZipCode ?? $station->subdistrict->ZipCode }}
                    </span>
                </p>
                <input type="hidden" id="ip-qt-customerid" value="{{ $customer->CustomerID }}">
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


                <!-- 🔸 ราคารวม -->
                <div class="text-end mt-3">
                    <h5>รวมทั้งหมด: <span id="qt-total-price">0.00</span> บาท</h5>
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                <button type="button" class="btn btn-success" id="qt-print-btn">พิมพ์ใบเสนอราคา</button>
            </div>

        </div>
    </div>
</div>

<script>
    $('#qt-update-btn').on('click', function() {
        let totolcharge = $('#ip-qt-totolcharge').val();
        let totoltarvel = $('#ip-qt-totoltarvel').val();
        let totalprice = $('#ip-qt-totalprice').val();
        totolcharge = parseFloat((totolcharge + '').replace(/,/g, '')) || 0;
        totoltarvel = parseFloat((totoltarvel + '').replace(/,/g, '')) || 0;
        totalprice = parseFloat((totalprice + '').replace(/,/g, '')) || 0;



        const discountType = $('#ip-qt-discounttype').val();
        const discountUnit = $('#ip-qt-discountunit').val();
        const discountValue = parseFloat($('#ip-qt-discount').val()) || 0;

        let baseAmount = 0;
        let textdiscount = '';

        switch (discountType) {
            case 'service':
                baseAmount = totolcharge;
                textdiscount = "ค่าบริการปรับมาตรวัดพร้อมตีตราหัวจ่ายน้ำมัน"
                break;
            case 'travel':
                baseAmount = totoltarvel;
                textdiscount = "ค่าเดินทาง"

                break;
            case 'total':
                baseAmount = totalprice;
                break;
            default:
                baseAmount = 0;
        }

        // คำนวณส่วนลดตามหน่วย
        let discountAmount = 0;
        if (discountUnit === 'percent') {
            discountAmount = (discountValue / 100) * baseAmount;
        } else {
            discountAmount = discountValue;
        }

        // ป้องกันส่วนลดเกินยอดที่ลด
        discountAmount = Math.min(discountAmount, baseAmount);

        const finalTotal = totalprice - discountAmount;
        if (discountAmount > 0) {
            // ลบแถวส่วนลดและราคาสุทธิเก่า ถ้ามี
            $('#qt-service-table tbody tr.discount-row, #qt-service-table tbody tr.total-row').remove();

            html = `
                <tr class="table-danger discount-row">
                    <td colspan="3" class="text-end"><strong>ส่วนลด${textdiscount}:</strong></td>
                    <td class="text-end"><strong>${discountAmount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</strong></td>
                </tr>
                <tr class="table-success total-row">
                    <td colspan="3" class="text-end"><strong>ราคาสุทธิ:</strong></td>
                    <td class="text-end"><strong>${finalTotal.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</strong></td>
                </tr>`;

            $('#qt-service-table tbody').append(html);
        }

        $('#qt-total-price').text(finalTotal.toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }));
    });
</script>