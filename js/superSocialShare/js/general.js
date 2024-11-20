$(document).ready(function() {
	$('.plugin_container select').change(function() {
		var select_val = $(this).val();
		$('.plugin_container .supersocialshare.bubble').attr('class', 'supersocialshare bubble '+$(this).val());
	});
});