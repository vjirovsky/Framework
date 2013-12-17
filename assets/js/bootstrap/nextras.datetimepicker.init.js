/**
 * This file is part of the Nextras community extensions of Nette Framework
 *
 * @license    MIT
 * @link       https://github.com/nextras
 * @author     Jan Skrasek
 */


// datepicker nette.ajax.js extension
$.nette.ext('datepicker', {
	load: function () {
		jQuery('.date, .datetime-local').each(function(i, el) {
			el = jQuery(el);
			el.get(0).type = 'text';
			el.datetimepicker({
				startDate: el.attr('min'),
				endDate: el.attr('max'),
				weekStart: 1,
				minView: el.is('.date') ? 'month' : 'hour',
				format: el.is('.date') ? 'd. m. yyyy' : 'd. m. yyyy - hh:ii',
				autoclose: true,
				language: 'cs' // required for localization
			});
			el.attr('value') && el.datetimepicker('setValue');
		});

		if (jQuery('#hideAll').length > 0 && (jQuery('.date').length > 0 || jQuery('.datetime-local').length > 0)) {
			jQuery('#hideAll').css('visibility', 'hidden');
		}
	}
});

