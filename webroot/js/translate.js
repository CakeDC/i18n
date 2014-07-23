/**
 * Copyright 2009-2014, Cake Development Corporation (http://cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2009-2014, Cake Development Corporation (http://cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

String.prototype.stripBr = function(){
	return this.replace(/<br\s*\/?>/mg, "");
}

// Translate all empty translation boxes
function translateFields() {
		//var area = $(this).parent('.translation');
		var area = $('.translation');
		area.append('<a href="#" class="translate-message">Translate</a>');
		$('a.translate-message').live('click', function() {
			var area = $(this).parent('.translation');
			area.find('.translate').each(function() {
				var self = $(this);
				var text = self.html().stripBr();
				if (text != '') {
					google.language.translate(text, "en", "fr", function(result) {
						if (!result.error) {
							self.html(result.translation);
						} else {
							alert('An error occured when trying to translate "' + text + '"');
						}
					});
				};
			});
			return false;
	});
}
google.load("language", "1");
google.setOnLoadCallback(translateFields);
