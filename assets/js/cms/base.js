$(document).ready(function() {

	// 1. tooltip
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


	// sortable rows
	$("#sortable").sortable({
		delay: 200,
		distance: 15,

		update: function (event, ui) {
			var rankList = $('#sortable').sortable('toArray').toString();
			var sortLink = $('#sortable').data("sort-link");
			$.post(sortLink, { data: rankList });
		}
	});


	// sortable rows for grid table
	$(".tbodySortable table tbody").sortable({
		delay: 200,
		distance: 15,

		update: function (event, ui) {
			var rankList = $('.tbodySortable table tbody').sortable('toArray').toString();
			var sortLink = $('.tbodySortable').data("sort-link");
			$.post(sortLink, { data: rankList });
		}
	});

	// sortable nested menu
	$('.frontMenu').nestable({
		'maxDepth': 2

	}).on('change', function() {
		var sortLink = $(this).data("sort-link");
		var menuList = $(this).nestable('serialize');
		menuList = window.JSON.stringify(menuList);

		console.log(menuList);

		$.get(sortLink, {
			data: menuList
		});
	});

});


// ajax
jQuery(window).load(function () {
	jQuery.nette.ext('init').linkSelector = 'a.ajax';
	jQuery.nette.ext('init').formSelector = 'form.ajax';
	jQuery.nette.init();
});
