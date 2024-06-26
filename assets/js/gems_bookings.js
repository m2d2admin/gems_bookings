jQuery(document).ready(function($) {

	// Initialize date selection
	$("#gl_dateofbirth, #sah_dateofbirth").datepicker({
		autoclose: true,
		todayHighlight: true,
		format: 'dd/mm/yyyy'
	}).datepicker('update', new Date());

	// Initialize value selection
	$('.form-select').select2({
		//allowClear: true,
		//placeholder: 'Selecteer'
	});

	//$('body').on('DOMNodeInserted', 'select', function () {
	//	$(this).select2();
	//});

});
