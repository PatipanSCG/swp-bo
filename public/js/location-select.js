function setupLocationSelect(provinceSelector, districtSelector, subdistrictSelector, postcodeInputSelector) {
    $(provinceSelector).on('change', function () {
        const provinceId = $(this).val();
        $(districtSelector).html('<option>-- โหลดอำเภอ... --</option>').prop('disabled', true);
        $(subdistrictSelector).html('<option>-- เลือกตำบล --</option>').prop('disabled', true);
            console.log(provinceId)

        if (provinceId) {
            console.log(1111)
            $.ajax({
                url: `/api/provinces/${provinceId}/districts`,
                type: 'GET',
                success: function (res) {
            console.log(res)

                    $(districtSelector).empty().append('<option value="">-- เลือกอำเภอ --</option>');
                    $.each(res, function (key, value) {
                        $(districtSelector).append(`<option value="${value.Id}">${value.NameInThai}</option>`);
                    });
                    $(districtSelector).prop('disabled', false);
                }
            });
        }
    });

    $(districtSelector).on('change', function () {
        const districtId = $(this).val();
        $(subdistrictSelector).html('<option>-- โหลดตำบล... --</option>').prop('disabled', true);

        if (districtId) {
            $.ajax({
                url: `/api/districts/${districtId}/subdistricts`,
                type: 'GET',
                success: function (res) {
                    $(subdistrictSelector).empty().append('<option value="">-- เลือกตำบล --</option>');
                    $.each(res, function (key, value) {
                        $(subdistrictSelector).append(`<option value="${value.Id}" data-postcode="${value.ZipCode}">${value.NameInThai}</option>`);
                    });
                    $(subdistrictSelector).prop('disabled', false);
                }
            });
        }
    });

    $(subdistrictSelector).on('change', function () {
        const selectedOption = $(this).find('option:selected');
        const postcode = selectedOption.data('postcode');
        if (postcodeInputSelector) {
            $(postcodeInputSelector).val(postcode);
        }
    });
}
