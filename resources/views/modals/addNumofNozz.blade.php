<div class="modal fade" id="addlocationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content"> <!-- ✅ เพิ่มบรรทัดนี้ -->
            <div class="modal-header">
                <h5 class="modal-title">เพิ่มจำนวนหัวจ่าย</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                    <input type="Hidden" id="ip-addlink-stationid" name="ip-addlink-stationid" class="form-control" value="{{$station->StationID}}">
           
                <div class="form-group ">
                    <label for="LinkGoogleMap">Link Google Map</label>
                    <input type="text" id="ip-nozznum"name="ip-linkgooglemap" class="form-control">
                </div>
                 <button type="button" class="btn btn-primary" id="btn-save-linkgooglemap">บันทึก
            </button>
            </div>

           
        </div>
    </div>
</div>