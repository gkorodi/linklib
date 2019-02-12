	<script src="assets/js/jquery-1.12.4.min.js"></script>
	<script src="assets/js/bootstrap.min.js"></script>
	<script src="assets/js/retina-1.1.0.js"></script>
	<script src="assets/js/jquery.hoverdir.js"></script>
	<script src="assets/js/jquery.hoverex.min.js"></script>
	<script src="assets/js/jquery.prettyPhoto.js"></script>
	<script src="assets/js/jquery.isotope.min.js"></script>
	<script src="assets/js/custom.js"></script>
	
	<script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"> </script>

	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-64712928-2"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());
		gtag('config', 'UA-64712928-2');
	</script>


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
					console.log("Link "+linkId+" deleted.")
					$('#row'+linkId).css('display','none');
				} else {
					$('#row'+linkId).css('background-color','red');
					console.log(respObj);
				}
				$('#blue').css('background-color',originalBgColor);
			}
		});
	}

	function tagLink(linkId, value) {
		var originalBgColor = $('#blue').css('background-color');
		$('#blue').css('background-color','lightGreen');
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
				$('#blue').css('background-color', originalBgColor);
			},
			data: {
				"method":"updatelink",
				"id":linkId,
				"column":"tags",
				"value":value
			}
		});
	}
</script>
