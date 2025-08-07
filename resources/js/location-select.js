        $('#ip-province').on('change', function() {
            const provinceId = $(this).val();
            console.log(provinceId);
            $('#ip-district').html('<option>-- โหลดอำเภอ... --</option>').prop('disabled', true);
            $('#ip-subdistrict').html('<option>-- เลือกตำบล --</option>').prop('disabled', true);

            if (provinceId) {
                $.ajax({
                    url: `/api/provinces/${provinceId}/districts`,
                    type: 'GET',
                    success: function(res) {
                        $('#ip-district').empty().append('<option value="">-- เลือกอำเภอ --</option>');
                        $.each(res, function(key, value) {
                            $('#ip-district').append(`<option value="${value.Id}">${value.NameInThai}</option>`);
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
                    url: `/api/districts/${districtId}/subdistricts`,
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
                        console.log(5555555555555)

            // ดึงค่า data-postcode
            const postcode = selectedOption.data('postcode');

            // แสดงผล หรือเอาไปใส่ใน input อื่น
            console.log("รหัสไปรษณีย์:", postcode);
            $('#ip-postcode').val(postcode); // กรณีคุณมี input เก็บ postcode

        });