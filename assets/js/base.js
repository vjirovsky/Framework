jQuery(document).ready(function() {

	// form on change submit
	jQuery(function () {
		jQuery('input.onChangeSubmit').change(function () {
			jQuery(this).closest('form').submit();
		});
	});

	// confirm alert
	jQuery('*[data-confirm]').on('click', function() {
		return confirm(jQuery(this).data('confirm'));
	});

});

jQuery(window).load(function () {
	jQuery.nette.ext('init').linkSelector = 'a.ajax';
	jQuery.nette.ext('init').formSelector = 'form.ajax';
	jQuery.nette.init();
});
