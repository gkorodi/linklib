	<script src="assets/js/jquery-1.12.4.min.js"></script>
	<script src="assets/js/bootstrap.min.js"></script>
	<script src="assets/js/retina-1.1.0.js"></script>
	<script src="assets/js/jquery.hoverdir.js"></script>
	<script src="assets/js/jquery.hoverex.min.js"></script>
	<script src="assets/js/jquery.prettyPhoto.js"></script>
	<script src="assets/js/jquery.isotope.min.js"></script>
	<script src="assets/js/custom.js"></script>
	
	<script src="//cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"> </script>

	<!-- Global site tag (gtag.js) - Google Analytics 
	<script async src="//www.googletagmanager.com/gtag/js?id=UA-64712928-2"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());
		gtag('config', 'UA-64712928-2');
	</script>-->


	<script>
		
		function getDateMetaTag(metadata) {
			console.log(metadata);
			
			var today = new Date();
			var dd = today.getDate();
			var mm = today.getMonth()+1; //January is 0!
			var yyyy = today.getFullYear();
			if ( dd < 10) { dd = '0'+dd; }
			if ( mm < 10) { mm = '0'+mm; }
			
			if (metadata.datePublished) {
				return metadata.datePublished;
			}
			
			if (metadata.date) {
				return metadata.date;
			}
			
			if (metadata.og_updated_time) {
				return metadata.og_updated_time;
			}
			
			if (metadata.article_published_time) {
				console.log("getDateMetaTag() picking field article_published_time")
				return metadata.article_published_time;
			}
			
			if (metadata.article_published_time) {
				return metadata.article_published_time;
			}
			
			if (metadata.published_at) {
				return metadata.published_at;
			}
			if (metadata.parsely_pub_date) {
				return metadata.parsely_pub_date;
			}
			
			if (metadata.articlepublished_time) {
				return metadata.articlepublished_time;
			}
			
			return '';
		}
		
	function repairLink(linkId, value) {
		$.ajax({
			type: 'GET',
			url: '_functions.php',
			dataType: 'json',
			success: function(respObj) {
				if (respObj.status == 'ok') {
					$('#row'+linkId).css('display','none');
				} else {
					alert(respObj.message);
				}
			},
			data: {
				"method":"updatelink",
				"id":linkId,
				"column":"tags",
				"value":value
			}
		});
	}

	function repairLink(linkId, value) {
		$.ajax({
			type: 'GET',
			url: '_functions.php',
			dataType: 'json',
			success: function(respObj) {
				if (respObj.status == 'ok') {
					$('#row'+linkId).css('display','none');
				} else {
					alert(respObj.message);
				}
			},
			data: {
				"method":"updatelink",
				"id":linkId,
				"column":"tags",
				"value":value
			}
		});
	}

	function tagCurate(linkId) {
		console.log("tagCurate("+linkId+")");
		
		var originalBgColor = $('#blue').css('background-color');
		$('#blue').css('background-color','orange');
		$.ajax({
			type: 'GET',
			url: '_functions.php',
			dataType: 'json',
			data: { "method":"tagCurate", "id":linkId },
		  success: function(respObj) {
				console.log(respObj);
				if (respObj.status == 'ok') {
					$('#blue').css('background-color','lightGreen');
					return true;
				} else {
					$('#blue').css('background-color','pink');
					$('#errormessage').html(respObj.message);
					return false;
				}
			}
		});
	}
	
	function tagLink(linkId, tags) {
		console.log('tagLink() starting');
		$('#row'+linkId).css('background-color','gray');
		
		$.getJSON( '_functions.php', {
			method: 'updateFieldById',
			id: linkId,
			field: 'tags',
			value: tags
		})
    .done(function( data ) {
			console.log(data);
			$('#row'+linkId).css('background-color','pink');
			if (data.status == 'ok') { 
				$('#row'+linkId).hide(); 
			} else {
				$('#row'+linkId).css('background-color','red');
			}
    })
		.fail(function(data) {
			console.log( "tagLink() error" );
			console.log(data);
		});
		
	}

	function deleteLink(linkId) {
		console.log("deleteLink("+linkId+")");

		var originalBgColor = $('#blue').css('background-color');
		
		$('#blue').css('background-color','lightGreen');
		$.ajax({
			type: 'GET',
			url: '_functions.php',
			dataType: 'json',
			data: {
				"method":"deletelink",
				"id":linkId
			},
		  success: function(respObj) {
				if (respObj.status == 'ok') {
					console.log("Link "+linkId+" deleted.");
					console.log($('#row'+linkId));
					$('#row'+linkId).css('display','none');
				} else {
					console.log(respObj);
					alert(respObj.message);
				}
				$('#blue').css('background-color', originalBgColor);
			},
			data: {
				"method":"updateTag",
				"id":linkId,
				"value":value
			}
		});
	}


	function hideLink(linkId) {
		console.log('hideLink() id:'+linkId);
		console.log($('#row'+linkId));
		
		//$('#row'+linkId).hide();
		$('#row'+linkId).css('display','none');
		
	}
</script>
