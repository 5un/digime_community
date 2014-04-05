<!DOCTYPE html>
<html>
	<head>
		<title>Live Poll Test</title>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
		<script>
			var rootPath = "<?php echo base_url(); ?>";
			$(document).ready(function(){
				
				// get a vote by id
				function test1(){
					var startTime = new Date();
					$.post(rootPath + 'index.php/live_poll/p',
					{poll_id:3},
					function(data,textStatus,jqXHR){
						var latency = (new Date()) - startTime;
						debugLog('data:' + data);
						debugLog('latency:' + latency + 'ms');
					},"text");
				}
				
				// get a vote that does not exists
				function test2(){
					var startTime = new Date();
					$.post(rootPath + 'index.php/live_poll/p',
					{poll_id:6000},
					function(data,textStatus,jqXHR){
						var latency = (new Date()) - startTime;
						debugLog('data:' + data);
						debugLog('latency:' + latency + 'ms');
					},"text");
				}
				
				// try to vote
				function test3(){
				
					vote(1,3,2,function(){
						vote(1,3,3,function(){
							vote(1,3,4,function(){
								vote(1,3,2);
							});
						});
					});
					
				}
				
				function test4(){
					debugLog('v1 called');
					vote(1,3,2,function(){debugLog('vote 1,3,2 -0');});
					debugLog('v2 called');
					vote(1,3,3,function(){debugLog('vote 1,3,3');});
					debugLog('v3 called');
					vote(1,3,4,function(){debugLog('vote 1,3,4');});
					debugLog('v4 called');
					vote(1,3,2,function(){debugLog('vote 1,3,2 -1');});
				}
				
				function test5(){
					vote(2,3,3, function(){ debugLog('vote 2,3,3'); });
					vote(3,3,2, function(){ debugLog('vote 3,3,2'); });
					vote(4,3,4, function(){ debugLog('vote 4,3,4'); });
					vote(5,3,2, function(){ debugLog('vote 5,3,2'); });
				}
				
				function test6(){
					vote(2,3,3, function(){ debugLog('vote 2,3,3'); });
					vote(3,3,3, function(){ debugLog('vote 3,3,3'); });
					vote(4,3,3, function(){ debugLog('vote 4,3,3'); });
					vote(5,3,3, function(){ debugLog('vote 5,3,3'); });
				}
				
				function vote(user_id, poll_id, ans_id,callback){
					var startTime = new Date();
					$.post(rootPath + 'index.php/live_poll/v',
					{user_id:user_id, poll_id:poll_id, ans_id:ans_id},
					function(data,textStatus,jqXHR){
						var latency = (new Date()) - startTime;
						debugLog('data:' + data);
						debugLog('latency:' + latency + 'ms');
						callback();
					},"text");
				}
				
				$('#btn_test1').click(function(e){
					test1();
				});
				
				function debugLog(msg){
					$('#debug_out').append(msg + '<br>');
				}
			});
		</script>
	</head>
	<body>
		<div id="debug_out"></div>
		<button id="btn_test1">Test1</button>
		<button id="btn_test2">Test2</button>
		<button id="btn_test3">Test3</button>
	</body>
</html>