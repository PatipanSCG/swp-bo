<div class="modal fade" id="editStationModal" tabindex="-1" role="dialog" aria-labelledby="editStationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form method="POST" action="{{ route('stations.update', $station->StationID) }}">
        @csrf
        @method('PUT')
        
        <div class="modal-header">
          <h5 class="modal-title">แก้ไขข้อมูลสถานี</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        
        <div class="modal-body">
          <div class="form-group">
            <label>ชื่อสถานี</label>
            <input type="text" name="StationName" class="form-control" value="{{ $station->StationName }}" required>
          </div>
          
          <div class="form-group">
            <label>เลขประจำตัวผู้เสียภาษี</label>
            <input type="text" name="TaxID" class="form-control" value="{{ $station->TaxID }}">
          </div>
          
          <div class="form-group">
            <label>ยี่ห้อ</label>
            <select class="form-control" name="BrandID" required>
              @foreach($brands as $brand)
              {{-- Fixed: changed 'selectde' to 'selected' --}}
              <option value="{{ $brand->BrandID }}" {{ $station->BrandID == $brand->BrandID ? 'selected' : '' }}>
                {{ $brand->BrandName }}
              </option>
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
              <option value="{{ $prov->Code }}" {{ $station->Province == $prov->Code ? 'selected' : '' }}>
                {{ $prov->NameInThai }}
              </option>
              @endforeach
            </select>
          </div>
          
          <div class="form-group">
            <label>อำเภอ</label>
            <select class="form-control" name="District" id="ip-district">
              @foreach($District as $district)
              {{-- Fixed: changed $station->Distric to $station->District --}}
              <option value="{{ $district->Code }}" {{ $station->District == $district->Code ? 'selected' : '' }}>
                {{ $district->NameInThai }}
              </option>
              @endforeach
            </select>
          </div>
          
          <div class="form-group">
            <label>ตำบล</label>
            <select class="form-control" name="Subdistrict" id="ip-subdistrict">
              @foreach($Subdistrict as $subdistrict)
              {{-- Fixed: changed $station->Subdistric to $station->Subdistrict --}}
              <option value="{{ $subdistrict->Code }}" {{ $station->Subdistrict == $subdistrict->Code ? 'selected' : '' }}>
                {{ $subdistrict->NameInThai }}
              </option>
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
      </form>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#ip-province').on('change', function() {
        const provinceCode = $(this).val();
        console.log('Selected province:', provinceCode);
        
        // Reset dependent dropdowns
        $('#ip-district').html('<option>-- โหลดอำเภอ... --</option>').prop('disabled', true);
        $('#ip-subdistrict').html('<option>-- เลือกตำบล --</option>').prop('disabled', true);
        $('#ip-postcode').val('');

        if (provinceCode) {
            $.ajax({
                url: window.APP_URL + `/api/provinces/${provinceCode}/districts`,
                type: 'GET',
                success: function(response) {
                    $('#ip-district').empty().append('<option value="">-- เลือกอำเภอ --</option>');
                    $.each(response, function(key, district) {
                        $('#ip-district').append(`<option value="${district.Code}">${district.NameInThai}</option>`);
                    });
                    $('#ip-district').prop('disabled', false);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading districts:', error);
                    alert('เกิดข้อผิดพลาดในการโหลดข้อมูลอำเภอ');
                }
            });
        }
    });

    $('#ip-district').on('change', function() {
        const districtCode = $(this).val();
        $('#ip-subdistrict').html('<option>-- โหลดตำบล... --</option>').prop('disabled', true);
        $('#ip-postcode').val('');
        
        if (districtCode) {
            $.ajax({
                url: window.APP_URL + `/api/districts/${districtCode}/subdistricts`,
                type: 'GET',
                success: function(response) {
                    console.log('Subdistricts response:', response);
                    $('#ip-subdistrict').empty().append('<option value="">-- เลือกตำบล --</option>');
                    $.each(response, function(key, subdistrict) {
                        // Handle different possible property names
                        const code = subdistrict.Code || subdistrict.Id;
                        const postcode = subdistrict.ZipCode || subdistrict.Postcode;
                        $('#ip-subdistrict').append(`<option value="${code}" data-postcode="${postcode}">${subdistrict.NameInThai}</option>`);
                    });
                    $('#ip-subdistrict').prop('disabled', false);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading subdistricts:', error);
                    alert('เกิดข้อผิดพลาดในการโหลดข้อมูลตำบล');
                }
            });
        }
    });
    
    $('#ip-subdistrict').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const postcode = selectedOption.data('postcode');
        
        if (postcode) {
            console.log("รหัสไปรษณีย์:", postcode);
            $('#ip-postcode').val(postcode);
        }
    });
});
</script>