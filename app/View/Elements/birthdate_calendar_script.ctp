<script>
	$(document).ready(function() {
		$('#birthdate').datepicker({
			dateFormat: 'yy-mm-dd', 
			changeMonth: true,
			changeYear: true,
			yearRange: 'c-100:c',
		});
	});
</script>