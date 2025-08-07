<!-- Modal -->
<div class="modal fade" id="addComModal" tabindex="-1" role="dialog" aria-labelledby="addComModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="addComForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addComModalLabel">เพิ่มการติดต่อ</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="ปิด">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="StationID" id="com-station-id" value="{{ $station->StationID }}">
                    <div class="form-group">
                        <label for="ComunicataeTypeID">กิจกรรม</label>
                        <select class="form-control" name="ComunicataeTypeID" id="ComunicataeTypeID" required>
                            <option value="">-- เลือกกิจกรรม --</option>
                            @foreach ($communicationTypeList as $type)
                            <option value="{{ $type->ComunicataeTypeID }}"
                                {{ old('ComunicataeTypeID', $selectedTypeID ?? '') == $type->ComunicataeTypeID ? 'selected' : '' }}>
                                {{ $type->ComunicataeName }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                   

                    <div class="form-group">
                        <label for="ComunicataeDetail">รายละเอียด</label>
                        <textarea class="form-control" name="ComunicataeDetail" id="ComunicataeDetail" rows="3" required></textarea>
                    </div>

                    
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">บันทึก</button>
                </div>
            </div>
        </form>
    </div>
</div>