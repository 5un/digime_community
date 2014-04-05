<!DOCTYPE html>
<html>
	<head>
		<title>Test Live QA module</title>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
		<script>
			rootPath = "<?php echo base_url();?>";
			$(document).ready(function(){
				
				function test1(){
					$.post(rootPath + 'index.php/live_qa/g',
					{live_session_id:3, offset:0, page_size:10},
					function(data,textStatus,jqXHR){
						debugLog('test1: ' + data);
					},"text");
				}
				
				function test2(){
					debugLog('test2');
					vote_with_cb(2,3, function(){
						debugLog('vote_cb');
						unvote_with_cb(2,3,function(){ debugLog('done'); });
					});
				}
				
				function test3(){
					debugLog('test3');
					vote(2,3);
					vote(3,4);
					vote(4,3);
					
				}
				
				function test4(){
					debugLog('test4');
					$.post(rootPath + 'index.php/live_qa/aq', 
					{live_session_id:3, user_id:5, question:'Hello, Who are you?'},
					function(data, textStatus, jqXHR){
						debugLog('data:'+data);
					},"text");
				}
				
				function vote(user_id, question_id){
					$.post(rootPath + 'index.php/live_qa/v',
					{user_id:user_id, question_id:question_id},
					function(data,textStatus,jqXHR){
						debugLog('data:'+data);
					}, "text");
				}
				
				function vote_with_cb(user_id, question_id, callback){
					$.post(rootPath + 'index.php/live_qa/v',
					{user_id:user_id, question_id:question_id},
					function(data,textStatus,jqXHR){
						debugLog('data:'+data);
						callback();
					}, "text");
				}
				
				function unvote(user_id, question_id){
					$.post(rootPath + 'index.php/live_qa/uv',
					{user_id: user_id, question_id: question_id},
					function(data, textStatus, jqXHR){
						debugLog('data:'+data);
					},"text");
				}
				
				function unvote_with_cb(user_id, question_id,callback){
					debugLog('unvoting');
					$.post(rootPath + 'index.php/live_qa/uv',
					{user_id: user_id, question_id: question_id},
					function(data, textStatus, jqXHR){
						debugLog('data:'+data);
						callback();
					},"text");
				}
				
				function clear_votes(question_id){
					debugLog('clearVotes ' + question_id);
					$.post(rootPath + 'index.php/live_qa/cv',
					{question_id:question_id},
					function(data, textStatus, jqXHR){
						debugLog('data:'+data);
					}, "text");
				}
				
				$('#btn_test1').click(function(e){
					debugLog('test1');
					test4();
				});
				
				$('#btn_test2').click(function(e){
					clear_votes(3);
				});
				
				debugLog('test');
				
				function debugLog(msg){
					$('#debug_out').append(msg+'<br>');
				}
			});
		</script>
	</head>
	<body>
		<h1>Test Live QA</h1>
		<div id="debug_out"></div>
		<button id="btn_test1">test1</button>
		<button id="btn_test2">test2</button>
	</body>
</html>