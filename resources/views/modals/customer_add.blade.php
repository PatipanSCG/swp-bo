<div class="modal fade" id="addCustomerModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content"> <!-- ✅ เพิ่มบรรทัดนี้ -->
            <div class="modal-header">
                <h5 class="modal-title">{{ isset($customer) ? 'แก้ไขข้อมูลลูกค้า' : 'เพิ่มข้อมูลลูกค้า' }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="POST" action="{{ isset($customer) ? route('customers.update', $customer->CustomerID) : route('customers.store') }}">
                    @csrf
                    @if(isset($customer))
                    @method('PUT')
                    @endif

                    {{-- ชื่อลูกค้า --}}
                    <div class="form-group">
                        <label for="CustomerName">ชื่อลูกค้า</label>
                        <input type="text" name="CustomerName" class="form-control" value="{{ old('CustomerName', $customer->CustomerName ?? '') }}" required>
                    </div>

                    {{-- ประเภทนิติบุคคล --}}
                    <div class="form-group">
                        <label for="CustomerType">ประเภทนิติบุคคล</label>
                        <select name="CustomerType" class="form-control" required>
                            <option value="">-- เลือกประเภท --</option>
                            @foreach (\App\Models\Customer::customerTypeList() as $key => $label)
                            <option value="{{ $key }}" {{ old('CustomerType', $customer->CustomerType ?? '') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- เลขผู้เสียภาษี --}}
                    <div class="form-group">
                        <label for="TaxID">เลขผู้เสียภาษี</label>
                        <input type="text" name="TaxID" class="form-control" value="{{ old('TaxID', $customer->TaxID ?? '') }}">
                    </div>

                    {{-- ที่อยู่ --}}
                    <div class="form-group">
                        <label for="Address">ที่อยู่</label>
                        <textarea name="Address" class="form-control">{{ old('Address', $customer->Address ?? '') }}</textarea>
                    </div>

                    {{-- จังหวัด / อำเภอ / ตำบล --}}
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="Province">จังหวัด</label>
                            <select name="Province" id="id-province" class="form-control" required>
                                <option value="">-- เลือกจังหวัด --</option>
                                @foreach($provinces as $province)
                                <option value="{{ $province->Id }}" {{ old('Province', $customer->Province ?? '') == $province->code ? 'selected' : '' }}>
                                    {{ $province->NameInThai }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="Distric">อำเภอ</label>
                            <select class="form-control" name="District" id="ip-district"></select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="Subdistric">ตำบล</label>
                          <select class="form-control" name="Subdistrict" id="ip-subdistrict"></select>
                        </div>
                    </div>

                    {{-- รหัสไปรษณีย์ / โทรศัพท์ / อีเมล --}}
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="Postcode">รหัสไปรษณีย์</label>
                            <input type="text" name="Postcode"  id="id-postcode" class="form-control" value="{{ old('Postcode', $customer->Postcode ?? '') }}">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="Telphone">เบอร์โทรศัพท์</label>
                            <input type="text" name="Telphone" class="form-control" value="{{ old('Telphone', $customer->Telphone ?? '') }}">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="Phone">มือถือ</label>
                            <input type="text" name="Phone" class="form-control" value="{{ old('Phone', $customer->Phone ?? '') }}">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="Email">อีเมล</label>
                            <input type="email" name="Email" class="form-control" value="{{ old('Email', $customer->Email ?? '') }}">
                        </div>
                    </div>

                    {{-- สถานะ --}}
                    <div class="form-group">
                        <label for="Status">สถานะ</label>
                        <select name="Status" class="form-control">
                            <option value="1" {{ old('Status', $customer->Status ?? '1') == '1' ? 'selected' : '' }}>ใช้งาน</option>
                            <option value="0" {{ old('Status', $customer->Status ?? '1') == '0' ? 'selected' : '' }}>ไม่ใช้งาน</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        {{ isset($customer) ? 'อัปเดตข้อมูล' : 'เพิ่มข้อมูล' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>