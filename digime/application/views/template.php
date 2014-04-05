<!DOCTYPE html>
<html>
	<head>
		<title>temp</title>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
		<script>
			var rootPath = "<?php echo base_url();?>";
			$(document).ready(function(){
				
				function debugLog(msg){ $('#debug_out').append(msg + '<br>'); }
				
				debugLog('trying to do GET');
				$.get(rootPath + 'index.php/welcome/request_type',
				({}),
				function(data, textStatus, jqXHR){
					debugLog('GET data:'+data);
				},"text");
				
				debugLog('trying to do POST');
				$.post(rootPath + 'index.php/welcome/request_type',
				({}),
				function(data, textStatus, jqXHR){
					debugLog('POST data:'+data);
				},"text");
				
			});
		</script>
	</head>
	<body>
		<div id="debug_out"></div>
	</body>
</html>