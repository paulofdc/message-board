<script>
	$(document).ready(function() {
		$('#profile-image-upload').change(function() {
			var file = this.files[0];
			if (file) {
				var reader = new FileReader();
				reader.onload = function(e) {
					$('#imagePreview').attr('src', e.target.result).show();
				}
				reader.readAsDataURL(file);
			}
		});
	});
</script>