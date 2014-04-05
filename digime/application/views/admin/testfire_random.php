<!DOCTYPE html>
<html>
	<head>	
		<title>test fire</title>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
		<script>
			$(document).ready(function(){
				var i = 0;
				var host = 'http://dmstg.digimagic.com.sg/digime/index.php/';
				
				function sendAnnouncement(announcement_title, announcement_body){
					$.post(host+'admin/notificationpanel/send_announcement',{announcement_title:announcement_title, announcement_body:announcement_body},
					function(data){
						$('#result-announcement').html(data);
					},"text");
				}
				
				function sendMessage(){
					var randVar = Math.floor(Math.random()*3);
					$('#result-announcement').html("randvar"+randVar);
					if(randVar < 0.005){
						$('#currentIndex').text(""+i);
						sendAnnouncement("announcement"+i, "This is announcement "+i);
						i++;
					}
				}
				
				setInterval(sendMessage,20000);
			});
		</script>
	</head>
	<body>
		<h1>Batch fire</h1>
		<h2 id="currentIndex">1</h2>
		<button id="btnStart">Start</button>
		<div id="result-announcement">log:</div>
	</body>
</html>