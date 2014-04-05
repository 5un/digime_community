<!DOCTYPE html>
<html>
	<head>
		<title>Test Upload</title>
		
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
		<script src="<?php echo base_url('assets/uploadify/jquery.uploadify-3.1.min.js'); ?>"></script>
		<script>
			$(document).ready(function(){
				 $("#file_upload_1").uploadify({
					height        : 30,
					swf           : '<?php echo base_url('assets/uploadify/uploadify.swf'); ?>',
					uploader      : '<?php echo base_url('index.php/resource/u'); ?>',
					width         : 120,
					onUploadSuccess : function(file, data, response) {
						alert('The file was saved to: ' + data);
					}
				});
			});
		</script>
		<link rel="stylesheet" type="text/css" 
		href="<?php echo base_url('assets/uploadify/uploadify.css'); ?>" />
	</head>
	<body>
		<h1>Uploadify Test</h1>
		<p>file will be added into the ResourceManager system</p>
		libpath: <?php echo base_url('assets/uploadify/jquery.uploadify-3.1.min.js'); ?>
		<br />
		<?php echo base_url('assets/uploadify/uploadify.swf'); ?>
		<div id="file_upload_1">
		
		</div>
	</body>
</html>