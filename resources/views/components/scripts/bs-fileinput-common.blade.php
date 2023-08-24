<script type="module">
    $(() => {
        var fields = document.querySelectorAll('.bs-fileinput');
        fields.forEach(field => {
            $(field).fileinput({
            showUpload: false,
            fileActionSettings: {
                showZoom: false,
                showRemove: true,
            },
            required: field.attributes.required,
        });
        });
    });
</script>
