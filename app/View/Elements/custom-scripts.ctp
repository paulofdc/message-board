<script>
    $(document).ready(function() {
        $('.select2-recipient').select2({
            width: 'resolve',
            theme: "classic"
        });

        $('#upload-btn').click(function() {
            $('#profile-image-upload').click();
        });
        
        $('#profile-image-upload').change(function() {
            var fileName = $(this).val().split('\\').pop();
        });
    });
</script>