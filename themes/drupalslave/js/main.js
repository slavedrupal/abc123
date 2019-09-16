jQuery(document).ready(function ($) {
	"use strict";
	$('#go-to-top').click(function () {
		$('body,html').animate({
		    scrollTop: 0
		}, 800);
		return false;
	});
	AOS.init();
});