var InputfieldPageName = {
	sanitize: function(name) {

		// replace leading and trailing whitespace 
		name = jQuery.trim(name);
		name = name.toLowerCase();  
		
		// replace azerbaijani symbols
		name = name.replace('\u0131', 'i');
		name = name.replace('\u0130', 'i');
		name = name.replace('\u018f', 'e');
		name = name.replace('\u0259', 'e');
		name = name.replace('\u015e', 'sh');
		name = name.replace('\u015f', 'sh');
		name = name.replace('\u00c7', 'ch');
		name = name.replace('\u00e7', 'ch');
		name = name.replace('\u011f', 'g');
		name = name.replace('\u00d6', 'o');
		name = name.replace('\u00f6', 'o');
		name = name.replace('\u00dc', 'u');
		name = name.replace('\u00fc', 'u');

		var srch;
		for(srch in config.InputfieldPageName.replacements) {
			var repl = config.InputfieldPageName.replacements[srch];
			if(name.indexOf(srch) > -1) {
				var re = new RegExp(srch, 'g'); 
				name = name.replace(re, repl); 
			}
		}

		// replace invalid with dash
		name = name.replace(/[^-_.a-z0-9 ]/g, '-');
	
		// convert whitespace to dash
		name = name.replace(/\s+/g, '-') 
	
		// convert multiple dashes or dots to single
		name = name.replace(/--+/g, '-'); 
	
		// convert multiple dots to single
		name = name.replace(/\.\.+/g, '.'); 
	
		// remove ugly combinations next to each other
		name = name.replace(/(\.-|-\.)/g, '-'); 
	
		// remove leading or trailing dashes, underscores and dots
		name = name.replace(/(^[-_.]+|[-_.]+$)/g, ''); 

		// make sure it's not too long
		if(name.length > 128) name = name.substring(0, 128); 
	
		return name;
	},

	updatePreview: function($t, value) {
		$t.parent('p').siblings(".InputfieldPageNameURL").children("strong").text((value.length > 0 ? value + '/' : ''))
	}
};

jQuery(document).ready(function($) {

	$(".InputfieldPageName").find("input[type=text]").keyup(function() {
		var value = InputfieldPageName.sanitize($(this).val());
		InputfieldPageName.updatePreview($(this), value); 
		
	}).blur(function() {
		var value = InputfieldPageName.sanitize($(this).val());
		$(this).val(value); 
		InputfieldPageName.updatePreview($(this), value); 
	}).keyup();
}); 
