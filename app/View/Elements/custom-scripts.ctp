<script>
    $(document).ready(function() {
        $('#upload-btn').click(function() {
            $('#profile-image-upload').click();
        });
        
        $('#profile-image-upload').change(function() {
            var fileName = $(this).val().split('\\').pop();
        });
    });
</script>