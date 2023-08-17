$(() => {
    $('#tilePhotoUploadTable input[type="file"]').on('change', function () {
        $(this).parent().parent().find('a').attr('href', URL.createObjectURL($(this)[0].files[0]));
        $(this).parent().parent().find('a img.preview').attr('src', URL.createObjectURL($(this)[0].files[0]));
    });

    $('#tilePhotoUploadTable .upload-link').on('click', function (event) {
        event.preventDefault();
        $(this).parent().parent().find('input[type="file"]').trigger('click');
    });
})