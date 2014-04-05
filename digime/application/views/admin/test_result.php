<!DOCTYPE html>
<html>
	<head>
		<title></title>
		<style>
			.test_sequence{ box-shadow:rgba(0,0,0,0.5) 0px 0px 24px; border-radius:12px; padding:10px;}
			.test_case{}
			.test_case_description{}
			.test_result_pass{ color:green; font-weight:800; position:absolute; left:200px; }
			.test_result_fail{ color:red; font-weight:800; position:absolute; left:200px;	}
		</style>
	</head>
	<body>
		<h1>Test Result</h1>
		<div class="test_sequence">
			<?php foreach($test_cases as $test_case){ ?>
				<div class="test_case">
					<span class="test_case_description"><?php echo $test_case->description; ?></span>
					<? if($test_case->result){ ?>
						<span class="test_result_pass">PASSED</span>
					<?php }else{ ?>
						<span class="test_result_fail">FAILED</span>
					<?php } ?>
				</div>
			<?php } ?>
		</div>
	</body>
</html>