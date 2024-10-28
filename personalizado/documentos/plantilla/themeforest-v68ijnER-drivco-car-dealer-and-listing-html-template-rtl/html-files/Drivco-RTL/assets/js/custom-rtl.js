(function ($) {
	"use strict";

		// category--slider

    
    // =======================================================================================
	// Shop Card 
	// =======================================================================================

	// Initialize the first child as active on page load
	$('.shop-big-img ul li:last-child').addClass('active');
	// Mouse enter event for shop-big-img div
	$('.shop-big-img').mouseenter(function () {
		// Remove active class from all li elements except the first child
		$('.shop-big-img ul li:not(:last-child)').removeClass('active');
	});
	// Mouse leave event for shop-big-img div
	$('.shop-big-img').mouseleave(function () {
		// Remove active class from all li elements except the first child
		$('.shop-big-img ul li:not(:last-child)').removeClass('active');
		// Add active class to the first child
		$('.shop-big-img ul li:last-child').addClass('active');
	});
	// Hover event for li elements
	$('.shop-big-img ul li').hover(function () {
		// Add active class to the current li and remove from siblings
		$(this).addClass('active').siblings().removeClass('active');
	});
}(jQuery));