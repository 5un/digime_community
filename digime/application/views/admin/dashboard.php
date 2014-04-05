<!DOCTYPE html>
<html>
	<head>
		<title>DigiMe: User Module</title>
		<link rel="stylesheet" href="<?php echo base_url('assets/css/main.css'); ?>" />
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
		<script type="text/javascript" language="javascript">
			rootPath = "<?php echo base_url();?>";
		</script>
	</head>
	<body>
		
		<div id="global_toolbar">
			<span id="digime_logo">DigiMe</span>
		</div>
		
		<div id="page_container">
			<h1 class="page_header">Modules</h1>
			<div id="dm_ui_container">
		
				<div id="content_container">
					<?php foreach($modules as $module){?>
						<div class="dm_modules">
							<img src="<?php echo base_url('assets/pic/'.$module->icon); ?>" />
							<div class="dm_modules_actions">
								
								<a href="<?php echo base_url('index.php/'.$module->name.'/dashboard'); ?>" class="module_button">Configure</a>
							</div>
							<div class="dm_modules_info">
								<h2><?php echo $module->name;?></h2>
								<p><?php echo $module->description; ?></p>
							</div>
						</div>
					<?php } ?>
				</div> 
			</div>
		</div>
		<p id="footer_company">digimagic communications pte ltd</p>
	</body>
</html>