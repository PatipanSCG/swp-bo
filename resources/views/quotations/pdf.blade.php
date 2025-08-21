<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ใบเสนอราคา {{ $quotation->DocNo }}</title>
    <style>
        body {
            font-family: 'garuda', 'norasi', 'thsarabun', sans-serif;
            font-size: 12px;
            line-height: 1.2;
            margin: 0;
            padding: 8px;
            min-height: 100vh;
            position: relative;
        }
        
        .main-content {
            margin-bottom: 120px; /* เว้นพื้นที่สำหรับ signature */
        }
        
        .header-section {
            border: 2px solid #000;
            padding: 10px;
            margin-bottom: 15px;
            background-color: #fafafa;
        }
        
        .header-row {
            display: table;
            width: 100%;
        }
        
        .header-left {
            display: table-cell;
            width: 50%;
            vertical-align: middle;
            padding-right: 10px;
        }
        
        .header-right {
            display: table-cell;
            width: 35%;
            text-align: center;
            vertical-align: middle;
            border-left: 2px solid #000;
            padding-left: 15px;
        }
        
        .logo-section {
            font-size: 12px;
            margin-bottom: 8px;
        }
        
        .scg-logo {
            background-color: #006600;
            color: white;
            padding: 3px 6px;
            font-weight: bold;
            border-radius: 3px;
            margin-right: 8px;
        }
        
        .company-name {
            font-size: 13px;
            font-weight: bold;
            color: #006600;
        }
        
        .company-address {
            font-size: 10px;
            margin: 3px 0;
            color: #333;
        }
        
        .document-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #000;
        }
        
        .subtitle {
            font-size: 14px;
            text-decoration: underline;
            color: #333;
            margin-bottom: 10px;
        }
        
        .print-date {
            font-size: 9px;
            margin-top: 15px;
            color: #666;
        }
        
        .info-section {
            margin: 15px 0;
            background-color: #f9f9f9;
            padding: 12px;
            border: 1px solid #ddd;
        }
        
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        
        .info-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 25px;
        }
        
        .info-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            border-left: 1px solid #ddd;
            padding-left: 15px;
        }
        
        .info-line {
            margin: 4px 0;
            font-size: 11px;
        }
        
        .label {
            font-weight: bold;
            display: inline-block;
            width: 85px;
            color: #333;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 10px;
            border: 2px solid #000;
        }
        
        .items-table th,
        .items-table td {
            border: 1px solid #000;
            padding: 6px 4px;
            text-align: left;
            vertical-align: top;
        }
        
        .items-table th {
            background-color: #e6f3ff;
            font-weight: bold;
            text-align: center;
            font-size: 9px;
            line-height: 1.1;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .col-no { width: 4%; }
        .col-item-no { width: 10%; }
        .col-desc { width: 43%; }
        .col-qty { width: 7%; }
        .col-uom { width: 6%; }
        .col-price { width: 12%; }
        .col-discount { width: 8%; }
        .col-amount { width: 10%; }
        
        .item-description {
            font-size: 9px;
            line-height: 1.3;
            font-weight: bold;
        }
        
        .item-detail {
            font-size: 8px;
            margin-top: 3px;
            color: #555;
            line-height: 1.2;
        }
        
        .remark-section {
            background-color: #fff9e6;
            padding: 8px;
        }
        
        .remark-title {
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 5px;
            color: #333;
        }
        
        .remark-content {
            font-size: 9px;
            line-height: 1.4;
        }
        
        .summary-section {
            margin-top: 20px;
            text-align: right;
        }
        
        .summary-table {
            width: 320px;
            float: right;
            border-collapse: collapse;
            border: 2px solid #000;
            font-size: 11px;
        }
        
        .summary-table td {
            border: 1px solid #000;
            padding: 6px 8px;
        }
        
        .summary-label {
            background-color: #f0f8ff;
            font-weight: bold;
            text-align: right;
            width: 200px;
        }
        
        .summary-amount {
            text-align: right;
            font-weight: bold;
            width: 120px;
            background-color: #fff;
        }
        
        .grand-total-row {
            background-color: #e6ffe6 !important;
            font-weight: bold;
        }
        
        .signature-section {
            position: fixed;
            bottom: 20px;
            left: 8px;
            right: 8px;
            clear: both;
        }
        
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #000;
            font-size: 10px;
            background-color: #fff;
        }
        
        .signature-table td {
            border: 1px solid #000;
            padding: 8px 6px;
            text-align: center;
            height: 70px;
            vertical-align: top;
        }
        
        .signature-title {
            font-weight: bold;
            margin-bottom: 40px;
            line-height: 1.1;
            color: #333;
        }
        
        .signature-name {
            margin-top: 25px;
            border-top: 1px solid #333;
            padding-top: 4px;
            font-size: 9px;
        }
        
        .clearfix {
            clear: both;
            margin-bottom: 30px;
        }
        
        /* Page break settings */
        @page {
            margin: 15mm;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .no-break {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <div class="main-content">
        <!-- Header -->
        <div class="header-section">
            <div class="header-row">
                <div class="header-left">
                    <div class="logo-section">
                        <span class="scg-logo">SCG</span>
                        <span class="company-name">บริษัท สยามวิศวกรรมปิโตรเลียม จำกัด (สำนักงานใหญ่)</span>
                    </div>
                    <div class="company-address">7/383 ถนนไทยเจริญ แขวงบุคคโล เขตธนบุรี กรุงเทพมหานคร 10600</div>
                    <div class="company-address">โทร: 02-1054333 แฟกซ์: 02-5132098</div>
                    <div class="company-address">เว็บไซต์: www.scggroup.com อีเมล: info@scggroup.com</div>
                    <div class="company-address">เลขประจำตัวผู้เสียภาษี: 0105534090498 สาขา: 00000</div>
                </div>
                <div class="header-right">
                    <div class="document-title">ใบเสนอราคา</div>
                    <div class="subtitle">Sales Quotes</div>
                    <div class="print-date">Print: {{ now()->format('d/m/Y H:i A') }}</div>
                </div>
            </div>
        </div>
    
    <!-- Document Info -->
    <div class="info-section">
        <div class="info-row">
            <div class="info-left">
                <div class="info-line">
                    <span class="label">ลูกค้า/Name:</span> {{ $quotation->CustomerName }}
                </div>
                <div class="info-line">
                    <span class="label">รหัส/Customer:</span> {{ $quotation->CustomerID ? 'C' . str_pad($quotation->CustomerID, 9, '0', STR_PAD_LEFT) : 'N/A' }}
                </div>
                <div class="info-line">
                    <span class="label">ที่อยู่/Address:</span>
                </div>
                <div style="margin-left: 85px; font-size: 10px; margin-top: 2px;">
                    {{ $quotation->Address }}
                </div>
                <div class="info-line" style="margin-top: 8px;">
                    <span class="label">เลขประจำตัวผู้เสียภาษี:</span> {{ $quotation->TaxID ?: '1234567890' }}
                </div>
            </div>
            <div class="info-right">
                <div class="info-line">
                    <span class="label">เลขที่/Document No.:</span> {{ $quotation->DocNo }}
                </div>
                <div class="info-line">
                    <span class="label">วันที่/Document Date:</span> {{ $quotation->IssuedDate ? $quotation->IssuedDate->format('d/m/y') : now()->format('d/m/y') }}
                </div>
                <div class="info-line">
                    <span class="label">การชำระเงิน/Payment Method:</span> {{ $quotation->PaymentMethod }}
                </div>
                <div class="info-line">
                    <span class="label">เงื่อนไขการชำระเงิน/Term:</span> {{ $quotation->PaymentTerm }} Days
                </div>
                <div class="info-line">
                    <span class="label">พนักงานขาย/Salesperson:</span> {{ $quotation->Salesperson ?: 'คุณสรวี หุ่น' }}
                </div>
                <div style="margin-left: 85px; font-size: 10px;">
                    โทร./Tel.: {{ $quotation->SalespersonPhone ?: '092-261-5378' }}
                </div>
                <div style="margin-left: 85px; font-size: 10px;">
                    อีเมล/E-mail: {{ $quotation->SalespersonEmail ?: 'akarin@scggroup.com' }}
                </div>
            </div>
        </div>
        
        <div class="info-row" style="margin-top: 5px;">
            <div class="info-left">
                <div class="info-line">
                    <span class="label">Job</span> {{ $quotation->JobNo ?: 'NON' }} หน่วยงาน
                </div>
                <div class="info-line">
                    <span class="label">Version</span> {{ $quotation->Version }}
                </div>
                <div class="info-line">
                    <span class="label">Credit Limit</span> {{ number_format($quotation->CreditLimit, 2) }}
                </div>
            </div>
        </div>
    </div>
    
    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th class="col-no">ลำดับ<br>No.</th>
                <th class="col-item-no">รหัสสินค้า<br>Item No.</th>
                <th class="col-desc">รายละเอียด<br>Description</th>
                <th class="col-qty">จำนวน<br>Qty UOM</th>
                <th class="col-uom">หน่วย<br>UOM</th>
                <th class="col-price">ราคาต่อหน่วย<br>Unit Price</th>
                <th class="col-discount">ส่วนลด %<br>Discount %</th>
                <th class="col-amount">จำนวนเงิน<br>Amount</th>
            </tr>
        </thead>
        <tbody>
            @php
                $itemCount = 1;
                $items = $quotation->Items ?: [];
            @endphp
            
            @foreach($items as $item)
            <tr>
                <td class="text-center">{{ $itemCount++ }}</td>
                <td class="text-center">{{ $item['item_code'] ?? '1010600023' }}</td>
                <td>
                    <div class="item-description">
                        <strong>{{ $item['name'] ?? $item['description'] }}</strong>
                    </div>
                    @if(isset($item['detail']) && $item['detail'])
                    <div class="item-detail">{{ $item['detail'] }}</div>
                    @endif
                    @if(isset($item['remark']) && $item['remark'])
                    <div class="item-detail">{{ $item['remark'] }}</div>
                    @endif
                </td>
                <td class="text-center">{{ number_format($item['qty'] ?? 2, 2) }}</td>
                <td class="text-center">{{ $item['unit'] ?? 'Set' }}</td>
                <td class="text-right">{{ number_format($item['unit_price'] ?? 78714.28, 2) }}</td>
                <td class="text-center">{{ $item['discount_percent'] ?? 30 }}</td>
                <td class="text-right">{{ number_format($item['total_price'] ?? 110199.99, 2) }}</td>
            </tr>
            @endforeach
            
            @if($quotation->Note)
            <tr>
                <td colspan="8" class="remark-section">
                    <div class="remark-title">หมายเหตุ/Remark</div>
                    <div class="remark-content">{{ $quotation->Note }}</div>
                </td>
            </tr>
            @endif
        </tbody>
    </table>
    
    <!-- Summary Section -->
    <div class="summary-section">
        <table class="summary-table">
            <tr>
                <td class="summary-label">รวมเงิน/Sub Total</td>
                <td class="summary-amount">{{ number_format($quotation->Subtotal, 2) }}</td>
            </tr>
            
            @if($quotation->DiscountAmount > 0)
            <tr>
                <td class="summary-label">ส่วนลด/Discount</td>
                <td class="summary-amount">{{ number_format($quotation->DiscountAmount, 2) }}</td>
            </tr>
            @endif
            
            <tr>
                <td class="summary-label">ยอดรวมก่อนภาษี/Total</td>
                <td class="summary-amount">{{ number_format($quotation->SubtotalAfterDiscount ?? ($quotation->Subtotal - $quotation->DiscountAmount), 2) }}</td>
            </tr>
            
            @if($quotation->VatEnabled && $quotation->VatAmount > 0)
            <tr>
                <td class="summary-label">VAT 7%</td>
                <td class="summary-amount">{{ number_format($quotation->VatAmount, 2) }}</td>
            </tr>
            @endif
            
            <tr class="grand-total-row">
                <td class="summary-label grand-total-row">รวมสุทธิ/Grand Total</td>
                <td class="summary-amount grand-total-row">{{ number_format($quotation->GrandTotal, 2) }}</td>
            </tr>
        </table>
    </div>
    
    <div class="clearfix"></div>
    </div> <!-- ปิด main-content -->
    
    <!-- Signature Section - Fixed at bottom -->
    <div class="signature-section no-break">
        <table class="signature-table">
            <tr>
                <td style="width: 25%;">
                    <div class="signature-title">ผู้จัดทำ<br>Issued By</div>
                    <div class="signature-name">
                        {{ $quotation->IssuedBy ?: 'Akarin Poonsima' }}<br>
                        Date {{ $quotation->IssuedDate ? $quotation->IssuedDate->format('d/m/y') : now()->format('d/m/y') }}
                    </div>
                </td>
                <td style="width: 25%;">
                    <div class="signature-title">ตรวจสอบโดย<br>Verified By</div>
                    <div class="signature-name">
                        {{ $quotation->VerifiedBy ?: 'Sawitree Jullawanich' }}<br>
                        Date {{ $quotation->VerifiedDate ? $quotation->VerifiedDate->format('d/m/y') : now()->format('d/m/y') }}
                    </div>
                </td>
                <td style="width: 25%;">
                    <div class="signature-title">อนุมัติโดย<br>Approved By</div>
                    <div class="signature-name">
                        {{ $quotation->ApprovedBy ?: 'Munin Parthomaree' }}<br>
                        Date {{ $quotation->ApprovedDate ? $quotation->ApprovedDate->format('d/m/y') : now()->format('d/m/y') }}
                    </div>
                </td>
                <td style="width: 25%;">
                    <div class="signature-title">ลูกค้ายอมรับในข้อเสนอนี้<br>Customer Approved</div>
                    <div class="signature-name">
                        <br>
                        Date ___/___/____
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>