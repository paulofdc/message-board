<script>
	$(document).ready(function() {
		$('#profile-image-upload').change(function(e) {
			var parentEvent = e;
            var fileName = $(this).val().split('\\').pop();
            var fileExtension = fileName.split('.').pop().toLowerCase();
            var allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            
            if ($.inArray(fileExtension, allowedExtensions) === -1) {
                alert('Only gif, jpg, jpeg, and png are allowed.');
                $(parentEvent.target).val('');
				return;
            }

			var file = this.files[0];
			if (file) {
				var reader = new FileReader();

				reader.onloadend = function(e) {
					var arr = (new Uint8Array(reader.result)).subarray(0, 4);
					var header = "";
					for(var i = 0; i < arr.length; i++) {
						header += arr[i].toString(16);
					}
					
					var fileSignatures = {
						'89504e47': 'png', '47494638': 'gif', 'ffd8ffe0': 'jpeg', 'ffd8ffe1': 'jpg', 
						'ffd8ffe2': 'jpg', 'ffd8ffe3': 'jpg', 'ffd8ffe8': 'jpg'
					};
					
					var signature = header.toLowerCase();
					if (fileSignatures.hasOwnProperty(signature)) {
						var fileExtension = fileSignatures[signature];
						var base64Data = btoa(String.fromCharCode.apply(null, new Uint8Array(reader.result)));
						var dataUrl = 'data:image/' + fileExtension + ';base64,' + base64Data;

						$('#imagePreview').attr('src', dataUrl).show();
					} else {
						alert('Invalid file format.');
						$(parentEvent.target).val('');
					}
				};
				
				reader.readAsArrayBuffer(file);
			}
		});
	});
</script>