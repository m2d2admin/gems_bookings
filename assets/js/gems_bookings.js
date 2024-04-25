jQuery(document).ready(function($) {

	// Initialize date selection
	$("#gl_dob, #sah_dob").datepicker({
		autoclose: true,
		todayHighlight: true,
		format: 'dd/mm/yyyy'
	}).datepicker('update', new Date());

	// Initialize value selection
	$('.form-select').select2({
		placeholder: 'Selecteer'
	});

});
