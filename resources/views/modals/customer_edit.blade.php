<div class="modal fade" id="editCustomerModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <form action="{{ route('customers.update', $customer->CustomerID) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">แก้ไขข้อมูลลูกค้า</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>ชื่อ</label>
                    <input type="text" name="CustomerName" value="{{ $customer->CustomerName }}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>เลขผู้เสียภาษี</label>
                    <input type="text" name="TaxID" value="{{ $customer->TaxID }}" class="form-control">
                </div>
                {{-- เพิ่มฟิลด์อื่น ๆ ตาม need --}}
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">อัปเดต</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
            </div>
        </div>
    </form>
  </div>
</div>
