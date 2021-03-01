<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha256-4+XzXVhsDmqanXGHaHvgh1gMQKX40OUvDEBTu8JcmNs=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>


<script src="assets/js/retina-1.1.0.js"></script>
<script src="assets/js/modernizr.js"></script>
<script src="assets/js/jquery.hoverdir.js"></script>
<script src="assets/js/jquery.hoverex.min.js"></script>
<script src="assets/js/jquery.prettyPhoto.js"></script>
<script src="assets/js/jquery.isotope.min.js"></script>

<script src="assets/js/custom.js"></script>

<script>
		
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
	
	function setLevel(linkId, level) {
		console.log('setLevel() starting');
		
		$('#row'+linkId).css('background-color','gray');
		$.getJSON( '_functions.php', {
			method: 'updateLevelById',
			id: linkId,
			field: 'level',
			value: level
		})
		.done(function( data ) {
			console.log("setLevel() done");
			$('#row'+linkId).css('background-color','pink');
			if (data.status == 'ok') { 
				$('#row'+linkId).hide(); 
			} else {
				$('#row'+linkId).css('background-color','red');
			}
		})
		.fail(function(data) {
			console.log("setLevel() error" );
			console.log(data);
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
		$('#blue').css('background-color','orange');
		
		$('#row'+linkId).css('background-color','pink');
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
					$('#row'+linkId).css('background-color','red');
					console.log(respObj);
				}
				$('#blue').css('background-color',originalBgColor);
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
