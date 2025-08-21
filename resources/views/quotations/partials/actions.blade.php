
<div class="btn-group" role="group">
    <button type="button" class="btn btn-sm btn-info btn-view-quotation" 
            data-id="{{ $quotation->QuotationID }}" 
            title="ดูรายละเอียด">
        <i class="fas fa-eye"></i>
    </button>
    
    <button type="button" class="btn btn-sm btn-warning btn-edit-quotation" 
            data-id="{{ $quotation->QuotationID }}" 
            title="แก้ไข">
        <i class="fas fa-edit"></i>
    </button>
    
    <button type="button" class="btn btn-sm btn-success btn-download-pdf" 
            data-id="{{ $quotation->QuotationID }}" 
            title="ดาวน์โหลด PDF">
        <i class="fas fa-file-pdf"></i>
    </button>
    
    @if($quotation->Status === 'draft')
    <button type="button" class="btn btn-sm btn-danger btn-delete-quotation" 
            data-id="{{ $quotation->QuotationID }}" 
            data-doc-no="{{ $quotation->DocNo }}" 
            title="ลบ">
        <i class="fas fa-trash"></i>
    </button>
    @endif
</div>