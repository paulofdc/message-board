<script>
	$(document).ready(function() {
		$('#birthdate').datepicker({
			dateFormat: 'yy-mm-dd', 
			changeMonth: true,
			changeYear: true,
			yearRange: 'c-100:c',
			showOn: 'button'
		}).next('button').button({
			icons: {
				primary: 'ui-icon-calendar'
			},
			text: false
		}).css({
			'background-color': 'transparent', 
			'border-color': 'black', 
			'font-size': '18px'
		});
	});
</script>