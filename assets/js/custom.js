(function($) {
	jQuery(document).ready(function(){

		// Load recent posts
		jQuery('#recent_posts').each(function() {
			$(this).html('Recent Posts');
		});
		
		jQuery('#random_list_table').each(function() {
			$(this).append('<tr><td>Cell</td></tr>');
		});

		jQuery('#hostlist').each(function() {
			$.get('_functions.php?method=getHostList', function(data) {
				var table = $('<table>').addClass('table');
				$.each(data.hostlist, function(idx, item) {
					var row = $('<tr>');
					row.append($('<td>').html('<a href="#">'+idx+'</a>'));
					row.append($('<td>').text(item));
					table.append(row);
				});
				$('#hostlist').append(table);
			});
		});
		
		jQuery('a[data-gal]').each(function() {
			jQuery(this).attr('rel', jQuery(this).data('gal'));
		});
		jQuery("a[data-rel^='prettyPhoto']").prettyPhoto({animationSpeed:'slow',theme:'light_square',slideshow:false,overlay_gallery: false,social_tools:false,deeplinking:false});
	}); 


})(jQuery);
