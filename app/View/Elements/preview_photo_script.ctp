<script>
	$(document).ready(function() {
		$('#profile-image-upload').change(function(e) {
			var defaultImage = 'https://www.gravatar.com/avatar/00000000000000000000000000000000?s=200&d=mp';
			var parentEvent = e;
            var fileName = $(this).val().split('\\').pop();
            var fileExtension = fileName.split('.').pop().toLowerCase();
            var allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            
            if ($.inArray(fileExtension, allowedExtensions) === -1) {
                alert('Only gif, jpg, jpeg, and png are allowed.');
                $(parentEvent.target).val('');
				$('#imagePreview').attr('src', defaultImage).show();
				return;
            }

			var file = this.files[0];
			if (file) {
				var reader = new FileReader();

				reader.onloadend = function(e) {
					try {
						var UiArrayResult = new Uint8Array(reader.result);
						var arr = UiArrayResult.subarray(0, 4);
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
							var base64Data = _arrayBufferToBase64(e.target.result);
							var dataUrl = 'data:image/' + fileExtension + ';base64,' + base64Data;
							$('#imagePreview').attr('src', dataUrl).show();
						} else {
							alert('Invalid file format.');
							$('#imagePreview').attr('src', defaultImage).show();
							$(parentEvent.target).val('');
						}
					} catch (error) {
						console.error('Error loading file:', error);
						alert('An error occured during reading of uploaded file.');
						$('#imagePreview').attr('src', defaultImage).show();
						$(parentEvent.target).val('');
					}
				};
				
				reader.readAsArrayBuffer(file);
			}
		});

		function _arrayBufferToBase64( buffer ) {
			var binary = '';
			var bytes = new Uint8Array( buffer );
			var len = bytes.byteLength;
			for (var i = 0; i < len; i++) {
				binary += String.fromCharCode( bytes[ i ] );
			}
			return window.btoa( binary );
		}

	});
</script>


