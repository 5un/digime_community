<!DOCTYPE html>
<html>
	<head>
		<title>test</title>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
		<script>
			$(document).ready(function(){
				var rootPath = "<?php echo base_url();?>";
				var $debugLogger = $('#debug_log');
				function debugLog(msg){ $('#debug_log').append(msg + '<br>');}
				function testcase(msg){
					$testcase_holder = $('<span></span>');
					$testcase_holder.addClass('testcase');
					$testcase_holder.append(msg);
					$debugLogger.append($testcase_holder);
				}
				function testcase_result(result){
					$testcase_result_holder = $('<span></span>');
					$testcase_result_holder.addClass('testcase_result');
					if(result){
						$testcase_result_holder.addClass('testcase_result_pass');
						$testcase_result_holder.append('passed');
					}else{
						$testcase_result_holder.addClass('testcase_result_fail');
						$testcase_result_holder.append('fail');
					}
					$debugLogger.append($testcase_result_holder);
					$debugLogger.append('<br>');
				}
				
				function testcase_time(time_elapsed){
					$testcase_time_holder = $('<span></span>');
					$testcase_time_holder.addClass('testcase_time');
					$testcase_time_holder.append('(' + time_elapsed + ' ms.)');
					$debugLogger.append($testcase_time_holder);
				}
				function testcase_json(data){
					$json_string_holder = $('<div></div>');
					$json_string_holder.addClass('testcase_json');
					$json_string_holder.append(JSON.stringify(data));
					$debugLogger.append($json_string_holder);
				}
				
				function preTest(){
					// cleanse the data tables
					
				}
				
				function test1(){
					debugLog('test1');
					$.post(rootPath + 'index.php/live_session/g',
					{query:'', offset:0, size:100},
					function(data,textStatus,jqXHR){
						debugLog('text:' + data);
					},
					"text");
				}
				
				function test2(){
					debugLog('test2');
					$.post(rootPath + 'index.php/live_session/g',
					{query:'9', offset:0, size:100},
					function(data, textStatus, jqXHR){
						debugLog('test:' + data);
					},
					"text");
				}
				
				function test3(){
					debugLog('test3');
					$.post(rootPath + 'index.php/live_session/attend',
					{user_id:1, live_session_id:2},
					function(data, textStatus, jqXHR){
						debugLog('text:' + data);
						test4();
					},
					"text");
				}
				
				function test4(){
					$.post(rootPath + 'index.php/live_session/attendance',
					{user_id:1, live_session_id:2},
					function(data, textStatus,jqXHR){
						debugLog('test4:' + data);
						test5();
					}, 
					"text");
				}
				
				function test5(){
					$.post(rootPath + 'index.php/live_session/unattend',
					{user_id:1, live_session_id:2},
					function(data,textStatus,jqXHR){
						debugLog('test5' + data);
						test6();
					},
					"text");
				}
				
				function test6(){
					$.post(rootPath + 'index.php/live_session/attendance',
					{user_id:1, live_session_id:2},
					function(data,textStatus,jqXHR){
						debugLog('test6' + data);
					},
					"text");
				}
				
				function test7(){
					for(var i=0;i<7;i++){
						$.post(rootPath + 'index.php/live_session/attend',
						{user_id:i, live_session_id:11},
						function(data,textStatus,jqXHR){
							debugLog('test7' + data);
						},
						"text");
					}
				}
				
				function test8(){
					for(var i=0;i<7;i++){
						$.post(rootPath + 'index.php/live_session/unattend',
						{user_id:i, live_session_id:10},
						function(data,textStatus,jqXHR){
							debugLog('test7' + data);
						},
						"text");
					}
				}
				
				function test9(index){
					$.post(rootPath + 'index.php/live_session/attend',
						{user_id:index, live_session_id:3},
						function(data,textStatus,jqXHR){
							debugLog('test9' + data);
							if(index<7){
								test9(index+1);
							}
						},
						"text");
				}
				
				// the real test
				function run_all(){
					// clear the whole table of attendance
					testcase('prepare for testing- clear all data:');
					var startTime = new Date();
					api_live_session('c', ({}), 
					function(data, textStatus, jqXHR){
						var timeElapsed = (new Date()) - startTime;
						testcase_time(timeElapsed);
						testcase_result(data);
						testcase_json(data); 
						test_1();
					});
				}
				
				function test_1(){
					// insert test data
					testcase('inserting data');
					var startTime = new Date();
					api_live_session('a',
					{title:"test_session1", description:"description for test_session1"},
					function(data, textStatus, jqXHR){
						var timeElapsed = (new Date()) - startTime;
						testcase_time(timeElapsed);
						testcase_result(data);
						testcase_json(data);
						test_1_2();
					});
				}
				
				function test_1_2(){
					testcase('getting data from insert');
					var startTime = new Date();
					$.post(rootPath + 'index.php/live_session/g',
					{query:'', offset:0, size:10},
					function(data,textStatus,jqXHR){
						var timeElapsed = (new Date()) - startTime;
						testcase_time(timeElapsed);
						testcase_result(data[0].title=='test_session1');
						testcase_json(data);
						test_2();
					},
					"json");
				}
				
				function test_2(){
					testcase('inserting more data');
					var startTime = new Date();
					api_live_session('a',
					{title:"test_session2", description:"description for test_session2"},
					function(data, textStatus, jqXHR){
						var timeElapsed = (new Date()) - startTime;
						testcase_time(timeElapsed);
						testcase_result(data);
						testcase_json(data);
						test_2_2();
					});
				}
				
				function test_2_2(){
					testcase('getting data from insert');
					var startTime = new Date();
					$.post(rootPath + 'index.php/live_session/g',
					{query:'', offset:0, size:10},
					function(data,textStatus,jqXHR){
						var timeElapsed = (new Date()) - startTime;
						testcase_time(timeElapsed);
						testcase_result(data[0].title=='test_session1');
						testcase_json(data);
					},
					"json");
				}
				
				function api_live_session(action, data, callback){
					$.post(rootPath + 'index.php/live_session/' + action,
						data,callback,"json");
				}
				
				$('#btn_test1').click(function(e){
					run_all();
				});
				debugLog('script_valid');
			});
		</script>
		<style>
			.testcase { color:gray; }
			.testcase_result { font-weight:800; position:absolute; left:400px; }
			.testcase_result_pass { color:green; }
			.testcase_result_fail { color:red; }
			.testcase_json { background-color:#cccccc; padding:5px; margin:5px;}
		</style>
	</head>
	<body>
		<h1>live session module test</h1>
		<div id="debug_log">debug:</div>
		<button id="btn_test1">test1</button>
	</body>
</html>