<div class="modal fade" id="editStationModal" tabindex="-1" role="dialog" aria-labelledby="editStationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <form method="POST" action="{{ route('stations.update', $station->StationID) }}">
      @csrf
      @method('PUT')
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">แก้ไขข้อมูลสถานี</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>ชื่อสถานี</label>
            <input type="text" name="StationName" class="form-control" value="{{ $station->StationName }}">
          </div>
          <div class="form-group">
            <label>เลขประจำตัวผู้เสียภาษี</label>
            <input type="text" name="TaxID" class="form-control" value="{{ $station->TaxID }}">
          </div>
          <div class="form-group">

            <label>ยี่ห้อ</label>
            <select class="form-control" name="BrandID" required>
              @foreach($brands as $brand)
              <option value="{{ $brand->BrandID }}" {{$station->BrandID == $brand->BrandId ? 'selectde' : ''}}>{{ $brand->BrandName }}</option>
              @endforeach
            </select>
          </div>

          <div class="form-group">
            <label>ที่อยู่</label>
            <input type="text" name="Address" class="form-control" value="{{ $station->Address }}">
          </div>
          <div class="form-group">
            <label>จังหวัด</label>
            <select class="form-control" name="Province" id="ip-province">
              @foreach($provinces as $prov)
              <option value="{{ $prov->Code }}" {{ $station->Province == $prov->Code ? 'selected' : '' }}>{{ $prov->NameInThai }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label>อำเภอ</label>
            <select class="form-control" name="District" id="ip-district">
              @foreach($District as $district)
              <option value="{{ $district->Code }}" {{ $station->Distric == $district->Code ? 'selected' : '' }}>{{ $district->NameInThai }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">

            <label>ตำบล</label>
            <select class="form-control" name="Subdistrict" id="ip-subdistrict">
              @foreach($Subdistrict as $subdistrict)
              <option value="{{ $subdistrict->Code }}" {{ $station->Subdistric == $subdistrict->Code ? 'selected' : '' }}>{{ $subdistrict->NameInThai }}</option>
              @endforeach
            </select>
          </div>

          <div class="form-group">
            <label>รหัสไปรษณีย์</label>
            <input type="text" class="form-control" name="Postcode" id="ip-postcode" value="{{ $station->Postcode }}">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
          <button type="submit" class="btn btn-primary">บันทึกการเปลี่ยนแปลง</button>
        </div>
      </div>
  </div>
  </form>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
 $('#ip-province').on('change', function() {
            const provinccode = $(this).val();
            console.log(provinccode);
            $('#ip-district').html('<option>-- โหลดอำเภอ... --</option>').prop('disabled', true);
            $('#ip-subdistrict').html('<option>-- เลือกตำบล --</option>').prop('disabled', true);

            if (provinccode) {
                $.ajax({
                    url: window.APP_URL+`/api/provinces/${provinccode}/districts`,
                    type: 'GET',
                    success: function(res) {
                        $('#ip-district').empty().append('<option value="">-- เลือกอำเภอ --</option>');
                        $.each(res, function(key, value) {
                            $('#ip-district').append(`<option value="${value.Code}">${value.NameInThai}</option>`);
                        });
                        $('#ip-district').prop('disabled', false);
                    }
                });
            }
        });

        $('#ip-district').on('change', function() {
            const districtId = $(this).val();
            $('#ip-subdistrict').html('<option>-- โหลดตำบล... --</option>').prop('disabled', true);
            console.log(districtId);
            if (districtId) {
                $.ajax({
                    url: window.APP_URL+`/api/districts/${districtId}/subdistricts`,
                    type: 'GET',
                    success: function(res) {
                        console.log(res)
                        $('#ip-subdistrict').empty().append('<option value="">-- เลือกตำบล --</option>');
                        $.each(res, function(key, value) {
                            $('#ip-subdistrict').append(`<option value="${value.Id}" data-postcode="${value.ZipCode}">${value.NameInThai}</option>`);
                        });
                        $('#ip-subdistrict').prop('disabled', false);
                    }
                });
            }
        });
        $('#ip-subdistrict').on('change', function() {
            // ดึง option ที่ถูกเลือก
            const selectedOption = $(this).find('option:selected');

            // ดึงค่า data-postcode
            const postcode = selectedOption.data('postcode');

            // แสดงผล หรือเอาไปใส่ใน input อื่น
            console.log("รหัสไปรษณีย์:", postcode);
            $('#ip-postcode').val(postcode); // กรณีคุณมี input เก็บ postcode

        });

</script>