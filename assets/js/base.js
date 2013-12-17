jQuery(document).ready(function() {
	// form on change submit
	jQuery(function () {
		jQuery('input.onChangeSubmit').change(function () {
			jQuery(this).closest('form').submit();
		});
	});

	// tooltip - @todo test
	// jQuery('*[data-tooltip]').tooltip(jQuery(this).data('tooltip'));

	// confirm alert
	jQuery('*[data-confirm]').on('click', function() {
		return confirm(jQuery(this).data('confirm'));
	});

	// hide flashes
	window.setTimeout(function() {
		jQuery('.alert.timeout').fadeTo(500, 0).slideUp(500, function(){
			jQuery(this).remove();
		});
	}, 2000);
});

jQuery(window).load(function () {
	jQuery.nette.ext('init').linkSelector = 'a.ajax';
	jQuery.nette.ext('init').formSelector = 'form.ajax';
	jQuery.nette.init();
});
