function getParam(name, url) {
    if (!url) {
      url = window.location.href;
    }
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}

function delete_link(linkid) {
	$('#linkdetails-'+linkid).hide();
}

function edit_link(linkid) {
	window.location='edit.php?id='+linkid;
}

(function($) {
	
	$('#btnDeleteLink').on('click', function() {
		console.log($(this));
		console.log($(this).parent());
	})
	
	
	jQuery(document).ready(function(){

		// Load recent posts
		jQuery('#recent_posts').each(function() {
			$(this).html('Recent Posts');
		});
		
		jQuery('#searchresults').each(function() {
			$.get('_functions.php?method=getSearchResults&q='+getParam('q'), function(data) {
				var table = $('<table>').addClass('table');
				$.each(data.searchresults.rows, function(idx, item) {
					var row = $('<tr>');
					row.append($('<td>').html('<a href="'+item[1]+'" target="_newWindow">'+item[2]+'</a><br /><small>'+(item[5]!=null?item[5]:'no tag')+'</small>'));
					row.append($('<td>').html('<a href="#" class="btn btn-info">Edit</button>'));
					table.append(row);
				});
				$('#searchresults').html(table);
			});
		});
		
		jQuery('a[data-gal]').each(function() {
			jQuery(this).attr('rel', jQuery(this).data('gal'));
		});
		jQuery("a[data-rel^='prettyPhoto']").prettyPhoto({animationSpeed:'slow',theme:'light_square',slideshow:false,overlay_gallery: false,social_tools:false,deeplinking:false});
	}); 


})(jQuery);
