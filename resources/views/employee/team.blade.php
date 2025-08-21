@extends('layouts.admin')

@section('main-content')
<!-- <h1 class="h3 mb-4 text-gray-800">📋 รายชื่อพนักงานทั้งหมด</h1>

@if (session('success'))
<div class="alert alert-success border-left-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif -->
<div class="container">
  <h3>รายการทีมช่าง</h3>
  <button class="btn btn-success mb-3" id="btn-add-team" data-toggle="modal" data-target="#createTeamModal">เพิ่มทีม</button>
  <table class="table table-bordered" id="team-table">
    <thead>
      <tr>
        <th>รหัสทีม</th>
        <th>ชื่อทีม</th>
        <th>รายละเอียด</th>
        <th>สมาชิก</th>
        <th>จัดการ</th>
      </tr>
    </thead>
  </table>
</div>


<!-- 🔧 Modal เพิ่มทีม-->
<!-- Modal: Create New Team -->
<div class="modal fade" id="createTeamModal" tabindex="-1" role="dialog" aria-labelledby="createTeamModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="createTeamForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createTeamModalLabel">สร้างทีมใหม่</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="ปิด">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="team_name">ชื่อทีม</label>
            <input type="text" class="form-control" name="name" id="team_name" required>
          </div>
          <div class="form-group">
            <label for="team_description">รายละเอียด</label>
            <textarea class="form-control" name="description" id="team_description" rows="3"></textarea>
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



<!-- 🔧 Modal แก้ไขสมาชิก -->
<!-- 🔧 Modal แก้ไขสมาชิก -->
<div class="modal fade" id="memberModal" tabindex="-1" aria-labelledby="memberModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">แก้ไขสมาชิกในทีม</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
      </div>
      <div class="modal-body">
        <div id="member-list" class="row"></div>
        <hr>
        <h6>เพิ่มสมาชิก</h6>
        <select class="form-control" id="employee-select"></select>
        <button class="btn btn-primary mt-2" id="btn-add-member">เพิ่ม</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
  $(function() {
    const table = $('#team-table').DataTable({
      ajax: {
        url: '{{env('APP_URL')}}teams',
        dataSrc: '' // ⭐️ บอกว่า array อยู่ root ของ JSON เลย
      },

      columns: [{
          data: 'id'
        },
        {
          data: 'name'
        },
        {
          data: 'description'
        },
        {
          data: 'members',
          render: function(data) {
            return data.map(m => m.user?.NameTH || '').join('<br>');
          }
        },
        {
          data: null,
          render: function(data) {
            return `
            <button class="btn btn-sm btn-warning edit-members" data-id="${data.id}">สมาชิก</button>
            <button class="btn btn-sm btn-danger delete-team" data-id="${data.id}">ลบ</button>
          `;
          }
        }
      ]
    });
    $('#createTeamForm').submit(function(e) {
      e.preventDefault();

      $.ajax({
        url: '{{env('APP_URL')}}teams/store',
        type: 'POST',
        data: $(this).serialize(),
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
          $('#createTeamModal').modal('hide');
          $('#createTeamForm')[0].reset();
          $('.modal-backdrop').remove();
          $('body').removeClass('modal-open');
          Swal.fire({
            icon: 'success',
            title: 'สร้างทีมสำเร็จแล้ว',
            showConfirmButton: false,
            timer: 2000
          });
          $('#team-table').DataTable().ajax.reload(null, false);
        },
        error: function(xhr) {

          $('#createTeamModal').modal('hide');
          $('#createTeamForm')[0].reset();
          $('.modal-backdrop').remove();
          $('body').removeClass('modal-open');
          Swal.fire({
            icon: 'Danger',
            title: 'เกิดข้อผิดพลาด: ' + xhr.responseText,
            showConfirmButton: true,

          });
        }
      });
    });
    // 📥 โหลดสมาชิกใน Modal
    $(document).on('click', '.edit-members', function() {
      const teamId = $(this).data('id');
      $('#memberModal').modal('show');

      $.get(`/teams/${teamId}/members`, function(res) {
        let html = '';
        res.members.forEach(m => {
          html += `
          <div class="col-md-8 mb-1">
            ${m.user.NameTH}
            </div>
            <div class="col-md-4 mb-1">
            <button class="btn btn-sm btn-outline-danger remove-member" data-id="${m.id}">ลบ</button>
          </div>`;
        });
        $('#member-list').html(html);

        $('#btn-add-member').data('team-id', res.id);
      });

      $.get('/employees/getDatainsystem', function(res) {
        $('#employee-select').empty();
        res.data.forEach(emp => {
          $('#employee-select').append(`<option value="${emp.UserID}">${emp.NameTH}</option>`);
        });
      });
    });

    // ➕ เพิ่มสมาชิก
    $('#btn-add-member').on('click', function () {
  const userId = $('#employee-select').val();
  const teamId = $(this).data('team-id');

  $.post({
    url: `{{env('APP_URL')}}teams/add-member`,
    data: {
      user_id: userId,
      team_id: teamId,
      _token: $('meta[name="csrf-token"]').attr('content')
    },
    success: function (res) {
      $('#memberModal').modal('hide');
      $('.modal-backdrop').remove();
      $('body').removeClass('modal-open');

      Swal.fire({
        icon: 'success',
        title: 'เพิ่มสมาชิกเรียบร้อยแล้ว',
        showConfirmButton: false,
        timer: 2000
      });

      let html = '';
      res.members.forEach(m => {
        html += `
          <div class="col-md-8 mb-1">
            ${m.user.NameTH}
          </div>
          <div class="col-md-4 mb-1">
            <button class="btn btn-sm btn-outline-danger remove-member" data-id="${m.id}">ลบ</button>
          </div>
        `;
      });
      $('#member-list').html(html);
              $('#team-table').DataTable().ajax.reload(null, false);

    },
    error: function (xhr) {
      if (xhr.status === 409) {
        Swal.fire('ไม่สามารถเพิ่มสมาชิก', xhr.responseJSON.message, 'warning');
      } else {
        Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถเพิ่มสมาชิกได้', 'error');
      }
    }
  });
});


    // ❌ ลบทีม
    $(document).on('click', '.delete-team', function() {
      const id = $(this).data('id');

      Swal.fire({
        title: 'คุณแน่ใจหรือไม่?',
        text: 'คุณต้องการลบทีมนี้หรือไม่',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'ใช่, ลบทันที!',
        cancelButtonText: 'ยกเลิก',
        reverseButtons: true
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: `{{env('APP_URL')}}teams/${id}`,
            type: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function() {
              Swal.fire({
                icon: 'success',
                title: 'ลบทีมสำเร็จแล้ว',
                showConfirmButton: false,
                timer: 2000
              });

              $('#team-table').DataTable().ajax.reload(null, false);
            },
            error: function() {
              Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถลบทีมได้', 'error');
            }
          });
        }
      });
    });
    // ❌ สมาชิกลบทีม
    $(document).on('click', '.remove-member', function() {
      const id = $(this).data('id');

      Swal.fire({
        title: 'คุณแน่ใจหรือไม่?',
        text: 'คุณต้องการลบสมาชิกออกจากทีมนี้หรือไม่',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'ใช่, ลบทันที!',
        cancelButtonText: 'ยกเลิก',
        reverseButtons: true
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: `{{env('APP_URL')}}teams/remove-member/${id}`,
            type: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
              // SweetAlert แสดงผลลัพธ์
              Swal.fire({
                icon: 'success',
                title: 'ลบสมาชิกสำเร็จแล้ว',
                showConfirmButton: false,
                timer: 2000
              });

              // สร้าง HTML ใหม่จากสมาชิกที่เหลือ
              let html = '';
              res.members.forEach(m => {
                html += `
              <div class="col-md-8 mb-1">
                ${m.user.NameTH}
              </div>
              <div class="col-md-4 mb-1">
                <button class="btn btn-sm btn-outline-danger remove-member" data-id="${m.id}">ลบ</button>
              </div>
            `;
              });

              $('#member-list').html(html);
            },
            error: function() {
              Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถลบสมาชิกได้', 'error');
            }
          });
        }
      });
    });

  });
</script>
@endsection