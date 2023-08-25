<script type="module">
    $(() => {
        var fields = document.querySelectorAll('.bs-fileinput');
        fields.forEach(field => {
            $(field).fileinput({
                allowedFileExtensions: ['jpg', 'png', 'tiff'],
                uploadUrl: field.attributes['data-upload-url'].value,
                uploadExtraData: {
                    // Get CSRF Token from meta tag
                    _token: document.querySelector('meta[name="csrf-token"]').content,
                },
                deleteUrl: field.attributes['data-delete-url'].value,
                deleteExtraData: {
                    // Get CSRF Token from meta tag
                    _token: document.querySelector('meta[name="csrf-token"]').content,
                },
                overwriteInitial: false,
                initialPreviewAsData: true,
                initialPreview: field.attributes['data-initial-preview'].value,
                initialPreviewConfig: field.attributes['data-initial-preview-config'].value,
                browseOnZoneClick: true,
                showUpload: false,
                fileActionSettings: {
                    showZoom: false,
                    showRemove: true,
                },
                required: field.attributes.required,
            }).on('filebatchselected', function(event, files) {
                $(this).fileinput('upload');
            }).on('filebatchuploadsuccess', function(event, data) {
                console.log('File batch upload success', data);
            }).on('filebatchuploaderror', function(event, data, msg) {
                console.log('File batch upload error', data, msg);
            }).on('fileuploaded', function(event, data) {
                console.log('Uploaded', data);
            }).on('filedeleted', function(event, key, jqXHR, data) {
                console.log('Deleted', data, key);
            });
        });
    });
</script>
