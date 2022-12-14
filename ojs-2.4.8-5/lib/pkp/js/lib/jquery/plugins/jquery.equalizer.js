/**
 * jquery.equalizer.js
 * 
 * Normalizes the heights of a set of elements.
 * 
 * Usage:  $('.someSelector').equalizeElementHeights();
 *
 * See: http://api.jquery.com/map
 * 
 * Copyright (c) 2013-2019 Simon Fraser University
 * Copyright (c) 2000-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 */
(function($){
	
	$.fn.equalizeElementHeights = function() {
		var maxHeight = this.map(function(index,element){
			return $(element).height();
		}).get();
		
		return this.height(Math.max.apply(this, maxHeight));
	};
})(jQuery);
