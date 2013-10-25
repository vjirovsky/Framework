$(document).ready(function() {

	// tooltip
	$("[rel=tooltip]").tooltip();


	// hide flashes
	window.setTimeout(function() {
		$(".flash.error").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove();
		});
		$(".flash.success").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove();
		});
	}, 2000);

	// chosen
	$('.chosen').chosen();

});


// ajax
jQuery(window).load(function () {
	jQuery.nette.ext('init').linkSelector = 'a.ajax';
	jQuery.nette.ext('init').formSelector = 'form.ajax';
	jQuery.nette.init();
});
