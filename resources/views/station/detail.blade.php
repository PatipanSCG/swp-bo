@extends('layouts.admin')


@section('main-content')
<div class="row">

    <div class="col-lg-4 order-lg-1">

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">ข้อมูลสถานี</h6>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-6 text-primary text-right">ชื่อสถานี :</div>
                    <div class="col-6">{{$station->StationName}}</div>
                </div>
                <div class="row">
                    <div class="col-6  text-primary text-right">เลขประจำตัวผู้เสียภาษี :</div>
                    <div class="col-6">{{$station->TaxID}}</div>
                </div>
                <div class="row">
                    <div class="col-6  text-primary text-right">ที่อยู่ :</div>
                    <div class="col-6">{{$station->Address}}</div>
                </div>
                <div class="row">
                    <div class="col-6  text-primary text-right">ตำบล :</div>
                    <div class="col-6">{{$station->subdistrict->NameInThai}}</div>
                </div>
                <div class="row">
                    <div class="col-6  text-primary text-right">อำเภอ :</div>
                    <div class="col-6">{{$station->district->NameInThai}}</div>
                </div>
                <div class="row">
                    <div class="col-6  text-primary text-right">จังหวัด :</div>
                    <div class="col-6">{{$station->province->NameInThai}}</div>
                </div> <div class="row">
                    <div class="col-6  text-primary text-right">รหัสไปรษณีย์ :</div>
                    <div class="col-6">{{$station->subdistrict->ZipCode}}</div>
                </div>
                <div class="row">
                    <div class="col-6  text-primary text-right">จำนวนตู้จ่าย :</div>
                    <div class="col-6">{{$station->dispensers_count}}</div>
                </div>
                <div class="row">
                    <div class="col-6  text-primary text-right">จำนวนหัวจ่าย :</div>
                    <div class="col-6">{{$station->nozzle_count}}</div>
                </div>

            </div>
        </div>

    </div>
    <div class="col-lg-4 order-lg-1">

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">ข้อมูลการออกใบกำกับภาษี</h6>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-6 text-primary text-right">ชื่อสถานี :</div>
                    <div class="col-6">{{$station->StationName}}</div>
                </div>
                <div class="row">
                    <div class="col-6  text-primary text-right">เลขประจำตัวผู้เสียภาษี :</div>
                    <div class="col-6">{{$customer}}</div>
                </div>
            </div>
        </div>

    </div>
    <div class="col-lg-4 order-lg-1">

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">สถานะสถานี</h6>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-6 text-primary text-right">ชื่อสถานี :</div>
                    <div class="col-6">{{$station->StationName}}</div>
                </div>
                <div class="row">
                    <div class="col-6  text-primary text-right">เลขประจำตัวผู้เสียภาษี :</div>
                    <div class="col-6"></div>
                </div>
            </div>
        </div>

    </div>
    <div class="col-lg-8 order-lg-2">

        <div class="card shadow mb-4">

            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">My Account</h6>
            </div>

            <div class="card-body">

                <form method="POST" action="{{ route('profile.update') }}" autocomplete="off">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <input type="hidden" name="_method" value="PUT">

                    <h6 class="heading-small text-muted mb-4">User information</h6>

                    <div class="pl-lg-4">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group focused">
                                    <label class="form-control-label" for="name">Name TH<span class="small text-danger">*</span></label>
                                    <input type="text" id="name" class="form-control" name="name" placeholder="Name" value="{{ old('name', Auth::user()->NameTH) }}">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group focused">
                                    <label class="form-control-label" for="last_name">Name EN</label>
                                    <input type="text" id="last_name" class="form-control" name="last_name" placeholder="Last name" value="{{ old('last_name', Auth::user()->NameEN) }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-control-label" for="email">Email address<span class="small text-danger">*</span></label>
                                    <input type="email" id="email" class="form-control" name="email" placeholder="example@example.com" value="{{ old('email', Auth::user()->mail) }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group focused">
                                    <label class="form-control-label" for="current_password">Current password</label>
                                    <input type="password" id="current_password" class="form-control" name="current_password" placeholder="Current password">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group focused">
                                    <label class="form-control-label" for="new_password">New password</label>
                                    <input type="password" id="new_password" class="form-control" name="new_password" placeholder="New password">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group focused">
                                    <label class="form-control-label" for="confirm_password">Confirm password</label>
                                    <input type="password" id="confirm_password" class="form-control" name="password_confirmation" placeholder="Confirm password">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Button -->
                    <div class="pl-lg-4">
                        <div class="row">
                            <div class="col text-center">
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </div>
                    </div>
                </form>

            </div>

        </div>

    </div>

</div>

@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {

    });
</script>
@endsection