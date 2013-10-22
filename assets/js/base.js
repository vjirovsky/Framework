$(document).ready(function() {

	// form on change submit
	$(function () {
		$("input.onChangeSubmit").change(function () {
			$(this).closest("form").submit();
		});
	});

	// confirm alert
	$("*[data-confirm]").on("click", function() {
		return confirm($(this).data("confirm"));
	});

	// hide flashes
	window.setTimeout(function() {
		$(".alert.timeout").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove();
		});
	}, 2000);

});


jQuery(window).load(function () {
	jQuery.nette.ext('init').linkSelector = 'a.ajax';
	jQuery.nette.ext('init').formSelector = 'form.ajax';
	jQuery.nette.init();
});
