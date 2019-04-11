<!DOCTYPE>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title></title>
	<link rel="stylesheet" href="">
	<script src="jquery.js" type="text/javascript" charset="utf-8"></script>
</head>
<body>
	<button type="button" onclick='abrir()'>ddddd</button>
	<script>
		
		$(document).ready(function() {
			setTimeout(function(){ $("body").html("ya"); }, 3000);
		});
		//alert("dff");
		window.opener.miSuperVariableFinalDeOpener = 9983212;
		console.log(window.opener.miSuperVariableFinalDeOpener);
		$('#principal', window.opener.document ).html('flipante.').focus();
		window.open('','parent').focus();

	</script>	
</body>
</html>
