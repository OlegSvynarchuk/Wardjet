/**
 * jQuery plugin for Responsive Hotspot
 *
 * Author: SK Lam
 */
(function() {
	'use strict';

	/*
		Reposition the HotSpots during init and resize windows
	*/
	function _positionHotspots(options) {
		var imageWidth = jQuery(options.mainselector + ' ' + options.imageselector).prop('naturalWidth');
		var imageHeight = jQuery(options.mainselector + ' ' + options.imageselector).prop('naturalHeight');

		var bannerWidth = jQuery(options.mainselector).width();
		var bannerHeight = jQuery(options.mainselector).height();
		console.log(imageWidth, imageHeight, bannerWidth, bannerHeight);
		jQuery(options.selector).each(function() {
			var xPos = jQuery(this).attr('x');
			var yPos = jQuery(this).attr('y');
			// xPos = xPos / imageWidth * bannerWidth;
			// yPos = yPos / imageHeight * bannerHeight;

			jQuery(this).css({
				'top': yPos+'%',
				'left': xPos+'%'
			});
			jQuery(this).children(options.tooltipselector).css({
				'margin-left': - (jQuery(this).children(options.tooltipselector).width() / 2)
			});
		});
	}

	// Bind the events (hover or click) for the tooltip
	function _bindHotspots(e, options) {
		if (jQuery(e).children(options.tooltipselector).is(':visible')) {
			jQuery(e).children(options.tooltipselector).css('display', 'none');
		} else {
			jQuery(options.selector + ' '  + options.tooltipselector).css('display', 'none');
			jQuery(e).children(options.tooltipselector).css('display', 'block');
			if (jQuery(window).width() - (jQuery(e).children(options.tooltipselector).offset().left + jQuery(e).children(options.tooltipselector).outerWidth()) < 0) {
				jQuery(e).children(options.tooltipselector).css({
					'right': '0',
					'left': 'auto',
				});
			}
		}
	}

	jQuery.fn.hotSpot = function( options ) {

		// Extend our default options with those provided.
		// Note that the first argument to extend is an empty
		// object – this is to keep from overriding our "defaults" object.
		var _options = jQuery.extend( {}, jQuery.fn.hotSpot.defaults, options );

		// Position each hotspot
		this.each(function() {
			_positionHotspots.call(jQuery(this), _options);
		});

		// Bind the windows resize event to recalculate the hotspot position
		jQuery(window).resize(function() {
			this.each(function() {
				_positionHotspots.call(jQuery(this), _options);
			});
		}.bind(this));

		// Bind the hover/click for selector to show the tooltip
		switch (_options.bindselector) {
			case 'click':
				jQuery(_options.selector).bind ('click', function(e){_bindHotspots(e.currentTarget, _options)});
				break;
			case 'hover':
				jQuery(_options.selector).hover (function(e){_bindHotspots(e.currentTarget, _options)});
				break;
			default:
				break;
		}

		return this;
	};

	// Plugin defaults
	jQuery.fn.hotSpot.defaults = {
		mainselector: '#hotspotImg',
		selector: '.hot-spot',
		imageselector: '.img-responsive',
		tooltipselector: '.tooltip',
		bindselector: 'hover'
	};
}(jQuery));
