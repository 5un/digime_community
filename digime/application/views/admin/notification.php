<!DOCTYPE html>
<html>
<head>
<title>Notification Panel</title>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script>
$(document).ready(function(){
	
	var host = '<?php echo base_url(); ?>index.php/';
	var path = 'admin/notificationpanel/';
	log(host);
	//var host = 'index.php/';
	
	$('#btn-send-announcement').click(function(e){
		var announcement_title = $('#txt-announcement-title').val();
		var announcement_body = $('#txt-announcement-body').val();
		$('#result-announcement').html("sending XML-RPC...");
		$.post(host+path+'send_announcement',{announcement_title:announcement_title, announcement_body:announcement_body},
		function(data){
			log('response' + data);
			$('#result-announcement').html(data);
		},"text");
	});

	$('#btn-send-notification').click(function(e){
	
	});
	
	$('#btn-send-livepoll').click(function(e){
		var livepoll_id = $('#txt-livepoll-id').val();
		log(livepoll_id);
		$('#result-livepoll').html("sending XML-RPC...");
		$.post(host+path+'send_livepoll',{id: livepoll_id},
		function(data){
			log('response'+data);
			$('#result-livepoll').html(data);
		},"text");
	});
	
	$('#btn-send-liveslide').click(function(e){
		var liveslide_id = $('#txt-liveslide-id').val();
		log(liveslide_id);
		$('#result-liveslide').html("sending XML-RPC...");
		$.post(host+path+'send_liveslide',{id: liveslide_id},
		function(data){
			log('response'+data);
			$('#result-liveslide').html(data);
		},"text");
	});
	
	function log(msg){
		$('#log').append('<br>'+msg);
	}
	
});
</script>
<style>
body {
   font-family: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif; 
   font-weight: 300;
}
	.notification-panel  {background-color:#cccccc; padding:10px;}
	.result-label {background-color:#555555; padding:5px; margin-top:5px; color:white;}
	#log {background-color:yellow; padding:5px;}
	.status-ok{ color:green; font-family:"Courier New", Courier, monospace;}
	.status-fail{ color:red; font-family:"Courier New", Courier, monospace;}
</style>
</head>
<body>
<h1>Admin Notification Sample Panel</h1>
<p>This page shows how to contact ejabberd via XML-RPC Request to send digime notifications<br />
For actual code, please see admin/notificationpanel.php</p>
<div class="notification-panel">
ejabberd host and port : <input type="text" id="txt-ejabberd-host" value="<?php echo $ejabberd_default_service_url; ?>"></input>
<p>The default configuration is for the scenario when ejabberd and php is running on the same machine.</p>
<b>Server Status:</b>
<span class="<?php if($ejabberd_status_ok) echo 'status-ok'; else echo 'status-fail';?>">
	<?php print_r($ejabberd_status); ?>
</span>
</div>
<br />

<div class="notification-panel">
<h3>Send Announcement</h3>
Title: <input type="text" id="txt-announcement-title"/>
Body: <input type="text" id="txt-announcement-body"/>
<button id="btn-send-announcement">Send Announcement</button>
<div class="result-label" id="result-announcement">result:</div>
</div>
<br />

<div class="notification-panel">
<h3>Send Notification</h3>
Object:<select id="sel-notification-object">
<option value="live_session">live_session</option>
<option value="news">news</option>
</select>
<button id="btn-send-notification">Send Notification</button>
<div class="result-label" id="result-notification">result:</div>
</div>
<br />

<div class="notification-panel">
<h3>Send Live Poll</h3>
Poll_id:<input type="text" value="" id="txt-livepoll-id"/>
<button id="btn-send-livepoll">Send Live Poll</button>
<div class="result-label" id="result-livepoll">result:</div>
</div>
<br />

<div class="notification-panel">
<h3>Send Live Slide</h3>
Live_slide_id:<input type="text" value="" id="txt-liveslide-id"/>
<button id="btn-send-liveslide">Send Live Slide</button>
<div class="result-label" id="result-liveslide">result:</div>
</div>
<br />

<div id="log">
log:
</div>

</body>
</html>