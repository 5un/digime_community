<!DOCTYPE html>
<html>
<head>
<title>Notification Panel</title>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/demo_table.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/main.css" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>assets/js/jquery.dataTables.js"></script>
<script>
$(document).ready(function(){
	var host = '<?php echo base_url(); ?>index.php/';
	log(host);
	//var host = 'index.php/';
	
	var call_url = host + 'admin/notificationpanel';
	
	// view elements
	var $blackOverlay = $('#black-overlay');
	var $popupHolder = $('#popup-box-holder');
	var $tableAnnouncementHistory = $('#table-announcement-history');
	
	var $popupContentSendNotification = $('#content-send-notification');
	var $popupContentSendLivePoll = $('#content-send-livepoll');
	var $popupContentSendLiveSlide = $('#content-send-liveslide');
	
	var selectedRowIndex = '';
	var selectedLiveSessionId = -1;
	var selectedLiveSessionRowId = '';
	var selectedLiveSlideRowId = '';
	var selectedLiveSlideId = -1;
	var selectedLivePollRowId = '';
	var selectedLivePollId = -1;
	var selectedNewsRowId = '';
	var selectedNewsId = -1;
	
	function showPopupAnimated(animated){
		if(animated){
			$blackOverlay.fadeIn('fast');
			$popupHolder.fadeIn('fast');
		}
	}
	
	function hidePopupAnimated(animated){
		if(animated){
			$blackOverlay.fadeOut('fast');
			$popupHolder.fadeOut('fast');
		}
	}
	
	function hidePopupContents(){
		$('#tab-send-notification-step1').show();
		$('#tab-send-notification-step2-livesession').hide();
		$('#tab-send-notification-step2-news').hide();
		$('#tab-send-liveslide-step1').show();
		$('#tab-send-liveslide-step2').hide();
		$popupContentSendNotification.hide();
		$popupContentSendLivePoll.show();
		$popupContentSendLiveSlide.hide();
	}
	
	// request functions
	function sendLiveSlide(){
		var liveslide_id = selectedLiveSlideId;
		log(liveslide_id);
		$('#result-liveslide').html("sending XML-RPC...");
		$.post(host+'admin/notificationpanel/send_liveslide',{id: liveslide_id},
		function(data){
			log('response'+data);
			$('#result-liveslide').html(data);
			dataTableLiveSlideHistory.fnDraw();
		},"text");
	}
	
	function sendLivePoll(){
		var livepoll_id = selectedLivePollId;
		log(livepoll_id);
		$('#result-livepoll').html("sending XML-RPC...");
		$.post(host+'admin/notificationpanel/send_livepoll',{id: livepoll_id},
		function(data){
			log('response'+data);
			$('#result-livepoll').html(data);
			dataTableLivePollHistory.fnDraw();
		},"text");
	}
	
	function sendNewsNotification(){
		var news_id = selectedNewsId;
		log(news_id);
		$('#result-notification').html("sending XML-RPC...");
		$.post(host+'admin/notificationpanel/send_news_notification',{news_id: news_id},
		function(data){
			log('response'+data);
			$('#result-notification').html(data);
			dataTableNotificationHistory.fnDraw();
		},"text");
	}
	
	function sendLiveSessionNotification(){
		var live_session_id = selectedLiveSessionId;
		$('#result-notification').html("sending XML-RPC...");
		$.post(host+'admin/notificationpanel/send_livesession_notification',{live_session_id: live_session_id},
		function(data){
			log('response'+data);
			$('#result-notification').html(data);
			dataTableNotificationHistory.fnDraw();
		},"text");
	}
	
	// buttons callback
	$('#btn-send-announcement').click(function(e){
		var announcement_title = $('#txt-announcement-title').val();
		var announcement_body = $('#txt-announcement-body').val();
		if(announcement_title=='' || announcement_body==''){
			alert('Title or Body is not filled');
			return;
		}
		
		$('#result-announcement').html("sending XML-RPC...");
		$.post(host+'admin/notificationpanel/send_announcement',{announcement_title:announcement_title, announcement_body:announcement_body},
		function(data){
			log('response' + data);
			$('#result-announcement').html(data);
			dataTableAnnouncementHistory.fnDraw();
		},"text");
	});

	$('#btn-send-notification').click(function(e){
		console.log(1);
		showPopupAnimated(true);
		$popupContentSendNotification.show();
		$popupContentSendLivePoll.hide();
		$popupContentSendLiveSlide.hide();
		$('#tab-send-notification-step1').show();
		$('#tab-send-notification-step2-livesession').hide();
		$('#tab-send-notification-step2-news').hide();
	});
	
	$('#btn-send-livepoll').click(function(e){
		$popupContentSendNotification.hide();
		$popupContentSendLivePoll.show();
		$popupContentSendLiveSlide.hide();
		showPopupAnimated(true);
	});
	
	$('#btn-send-liveslide').click(function(e){
		$popupContentSendNotification.hide();
		$popupContentSendLivePoll.hide();
		$popupContentSendLiveSlide.show();
		$('#tab-send-liveslide-step1').show();
		$('#tab-send-liveslide-step2').hide();
		showPopupAnimated(true);
	});
	
	$('#btn-send-liveslide-step1-next').click(function(e){
		$('#tab-send-liveslide-step1').hide();
		$('#tab-send-liveslide-step2').show();
		dataTableLiveSlide.fnDraw();
	});
	
	$('#btn-send-liveslide-step2-send').click(function(e){
		if(selectedLiveSlideId==-1){
			alert('a slide have not been selected');
			return;
		}
		console.log(selectedLiveSlideId);
		hidePopupAnimated(true);
		sendLiveSlide();
	});
	
	$('#btn-send-liveslide-step2-back').click(function(e){
		$('#tab-send-liveslide-step1').show();
		$('#tab-send-liveslide-step2').hide();
	});
	
	$('#btn-send-livepoll-step1').click(function(e){
		if(selectedLivePollId==-1){
			alert('a poll have not been selected');
			return;
		}
		sendLivePoll();
		hidePopupAnimated(true);
	});
	
	$('#btn-send-notification-step1-news').click(function(e){
		$('#tab-send-notification-step1').hide();
		$('#tab-send-notification-step2-livesession').hide();
		$('#tab-send-notification-step2-news').show();
	});
	
	$('#btn-send-notification-step1-livesession').click(function(e){
		$('#tab-send-notification-step1').hide();
		$('#tab-send-notification-step2-livesession').show();
		$('#tab-send-notification-step2-news').hide();
	});
	
	$('#btn-send-notification-step2-news-send').click(function(e){
		console.log(selectedNewsId);
		if(selectedNewsId==-1){
			alert('a news have not been selected');
			return;
		}
		sendNewsNotification();
		hidePopupAnimated(true);
	});
	
	$('#btn-send-notification-step2-livesession-send').click(function(e){
		if(selectedLiveSessionId==-1){
			alert('a live session have not been selected');
			return;
		}
		sendLiveSessionNotification();
		hidePopupAnimated(true);
	});
	
	$('#btn-cancel-popup').click(function(e){
		hidePopupAnimated(true);
	});
	
	function log(msg){
		$('#log').append('<br>'+msg);
	}
	
	//init
	
	$blackOverlay.hide();
	$popupHolder.hide();
	
	var dataTableAnnouncementHistory = $('#datatable-announcement-history').dataTable({
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": call_url + '/announcement_datatable',
		"aoColumns": [
			{ "mData": "id" },
			{ "mData": "title" },
			{ "mData": "body" },
			{ "mData": "created_at" }
		]
	});
	
	$('#datatable-livesession').dataTable({
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": call_url + '/livesession_datatable',
		"aoColumns": [
			{ "mData": "id" },
			{ "mData": "title" },
			{ "mData": "description" },
			{ "mData": "speaker" },
			{ "mData": "is_published" },
			{ "mData": "is_live" }
		]
	});
	
	$('#datatable-notification-livesession').dataTable({
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": call_url + '/livesession_datatable',
		"aoColumns": [
			{ "mData": "id" },
			{ "mData": "title" },
			{ "mData": "description" },
			{ "mData": "speaker" },
			{ "mData": "is_published" },
			{ "mData": "is_live" }
		]
	});
	
	$('#datatable-notification-news').dataTable({
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": call_url + '/news_datatable',
		"aoColumns": [
			{ "mData": "id" },
			{ "mData": "title" },
			{ "mData": "body" },
			{ "mData": "created_at" },
			{ "mData": "updated_at" },
			{ "mData": "tags" },
			{ "mData": "pic_id" },
			{ "mData": "is_published" },
			{ "mData": "num_views" }
		]
	});
	
	var dataTableLiveSlide = $('#datatable-liveslide').dataTable({
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": call_url + '/liveslide_datatable',
		"fnServerParams": function(aoData){
			aoData.push({"name":"live_session_id", "value":selectedLiveSessionId});
		},
		"aoColumns": [
			{ "mData": "id" },
			{ "mData": "live_session_id" },
			{ "mData": "title" },
			{ "mData": "res_id" },
			{ "mData": "is_published" },
			{ "mData": "is_presented" },
			{ "mData": "presented_at"}
		]	
	});
	
	var dataTableLivePoll = $('#datatable-livepoll').dataTable({
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": call_url + '/livepoll_datatable',
		"aoColumns": [
			{ "mData": "id" },
			{ "mData": "title" },
			{ "mData": "description" },
			{ "mData": "created_at" },
			{ "mData": "is_onetime_mode" },
			{ "mData": "is_private_mode" },
			{ "mData": "is_published"},
			{ "mData": "is_open"}
		]	
	});
	
	var dataTableNotificationHistory = $('#datatable-notification-history').dataTable({
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": call_url + '/notification_history_datatable',
		"aoColumns": [
			{ "mData": "id" },
			{ "mData": "user_id" },
			{ "mData": "message" },
			{ "mData": "created_at" },
			{ "mData": "is_read" },
			{ "mData": "type"}
		]	
	});
	
	var dataTableLivePollHistory = $('#datatable-livepoll-history').dataTable({
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": call_url + '/livepoll_history_datatable',
		"aoColumns": [
			{ "mData": "command" },
			{ "mData": "result" },
			{ "mData": "created_at" },
		]	
	});
	
	var dataTableLiveSlideHistory = $('#datatable-liveslide-history').dataTable({
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": call_url + '/liveslide_history_datatable',
		"aoColumns": [
			{ "mData": "command" },
			{ "mData": "result" },
			{ "mData": "created_at" },
		]	
	});
	
	$('#datatable-livesession tbody tr').live('click', function(){
		var id = this.id;
		console.log(id);
		if(selectedRowIndex==id){
			selectedRowIndex = -1;
			$(this).removeClass('row_selected');
			selectedLiveSessionId = -1;
		}else{
			$('#datatable-livesession tbody tr').removeClass('row_selected');
			selectedRowIndex = id;
			$(this).addClass('row_selected');
			selectedLiveSessionId = $(this).find('td').html();
		}			
	});
	
	$('#datatable-liveslide tbody tr').live('click', function(){
		var id = this.id;
		console.log(id);
		if(selectedLiveSlideRowId==id){
			selectedLiveSlideRowId = -1;
			$(this).removeClass('row_selected');
			selectedLiveSessionId = -1;
		}else{
			$('#datatable-liveslide tbody tr').removeClass('row_selected');
			selectedLiveSlideRowId = id;
			$(this).addClass('row_selected');
			selectedLiveSlideId = $(this).find('td').html();
		}			
	});
	
	$('#datatable-livepoll tbody tr').live('click', function(){
		var id = this.id;
		if(selectedLivePollRowId==id){
			selectedLivePollRowId = -1;
			$(this).removeClass('row_selected');
			selectedLivePollId = -1;
		}else{
			$('#datatable-livepoll tbody tr').removeClass('row_selected');
			selectedLivePollRowId = id;
			$(this).addClass('row_selected');
			selectedLivePollId = $(this).find('td').html();
		}			
	});
	
	$('#datatable-notification-news tbody tr').live('click', function(){
		var id = this.id;
		if(selectedNewsRowId==id){
			selectedNewsRowId = '';
			$(this).removeClass('row_selected');
			selectedNewsId = -1;
		}else{
			$('#datatable-notification-news tbody tr').removeClass('row_selected');
			selectedNewsId = id;
			$(this).addClass('row_selected');
			selectedNewsId = $(this).find('td').html();
		}
	});
	
	$('#datatable-notification-livesession tbody tr').live('click', function(){
		var id = this.id;
		if(selectedLiveSessionRowId==id){
			selectedLiveSessionRowId = '';
			$(this).removeClass('row_selected');
			selectedLiveSessionId = -1;
		}else{
			$('#datatable-notification-livesession tbody tr').removeClass('row_selected');
			selectedLiveSessionId = id;
			$(this).addClass('row_selected');
			selectedLiveSessionId = $(this).find('td').html();
		}
	});
	
	
});
</script>
</head>
<body>
<div class="container">
<?php $this->load->view('admin/navbar'); ?>
<h1>Admin Notification Center</h1>
<p>Use this page to send and monitor notifications</p>
<div class="panel">
ejabberd XML-RPC host and port : <input type="text" id="txt-ejabberd-host" value="<?php echo $ejabberd_default_service_url; ?>"></input>
<p>The default configuration is for the scenario when ejabberd and php is running on the same machine.</p>
<b>Server Status:</b>
<span class="<?php if($ejabberd_status_ok) echo 'status-ok'; else echo 'status-fail';?>">
	<?php print_r($ejabberd_status); ?>
</span>
</div>
<br />

<div class="panel">
<h3>Send Announcement</h3>
<h4>Announcement History</h4>
<div class="datatable-holder">
<table id="datatable-announcement-history">
	<thead>
		<tr>
			<th width="10%">id</th>
			<th width="25%">title</th>
			<th width="35%">body</th>
			<th width="25%">created_at</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
	<tfoot>
		<tr>
			<th>id</th>
			<th>title</th>
			<th>body</th>
			<th>created_at</th>
		</tr>
	</tfoot>
</table>
<br />
</div>
	<div>
		<h4>Send</h4>
		Title: <input type="text" id="txt-announcement-title"/><br />
		Body: <br />
		<textarea rows="4" cols="50" id="txt-announcement-body"></textarea><br />
		<button id="btn-send-announcement">Send Announcement</button>
		<div class="result-label" id="result-announcement">result:</div>
	</div>
</div>
<br />

<div class="panel">
<h3>Send Notification</h3>
<h4>Notification History</h4>
<div class="datatable-holder">
	<table id="datatable-notification-history">
		<thead>
			<tr>
				<th>id</th>
				<th>user_id</th>
				<th>message</th>
				<th>created_at</th>
				<th>is_read</th>
				<th>type</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
		<tfoot>
			<tr>
				<th>id</th>
				<th>user_id</th>
				<th>message</th>
				<th>created_at</th>
				<th>is_read</th>
				<th>type</th>
			</tr>
		</tfoot>
	</table>
	<br />
</div>
<button id="btn-send-notification">Send New Notification</button>
<div class="result-label" id="result-notification">result:</div>
</div>
<br />
<div class="panel">
<h3>Send Live Poll</h3>
<h4>Sending History</h4>
<div class="datatable-holder">
	<table id="datatable-livepoll-history">
		<thead>
			<tr>
				<th>command</th>
				<th>result</th>
				<th>sent at</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
		<tfoot>
			<tr>
				<th>command</th>
				<th>result</th>
				<th>sent at</th>
			</tr>
		</tfoot>
	</table>
	<br />
</div>
<br />
<button id="btn-send-livepoll">Send Live Poll</button>
<div class="result-label" id="result-livepoll">result:</div>
</div>
<br />

<div class="panel">
<h3>Send Live Slide</h3>
<h4>Sending History</h4>
<div class="datatable-holder">
	<table id="datatable-liveslide-history">
		<thead>
			<tr>
				<th>command</th>
				<th>result</th>
				<th>sent at</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
		<tfoot>
			<tr>
				<th>command</th>
				<th>result</th>
				<th>sent at</th>
			</tr>
		</tfoot>
	</table>
	<br />
</div>
<button id="btn-send-liveslide">Send Live Slide</button>
<div class="result-label" id="result-liveslide">result:</div>
</div>
<br />

<div id="log">
log:
</div>

<div id="black-overlay">

</div>
<div id="popup-box-holder">
	<div id="popup-box">
		<div id="popup-box-content">
		
		<div id="content-send-notification">
			<div id="tab-send-notification-step1">
				<h3>Send Notification</h3>
				<p>select notification type</p>
				<button id="btn-send-notification-step1-news">News</button>
				<button id="btn-send-notification-step1-livesession">Live Session</button>
			</div>
			<div id="tab-send-notification-step2-livesession">
				<table id="datatable-notification-livesession" cellpadding="0" cellspacing="0" border="0" class="display" style="font-size:8px;">
					<thead>
						<tr>
							<th width="5%">id</th>
							<th width="30%">title</th>
							<th width="45%">description</th>
							<th width="10%">speaker</th>
							<th width="5%">is_published</th>
							<th width="5%">is_live</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
					<tfoot>
						<tr>
							<th>id</th>
							<th>title</th>
							<th>description</th>
							<th>speaker</th>
							<th>is_published</th>
							<th>is_live</th>
						</tr>
					</tfoot>
				</table>
				<br />
				<button id="btn-send-notification-step2-livesession-send">Send</button>
			</div>
			<div id="tab-send-notification-step2-news">
				<table id="datatable-notification-news" cellpadding="0" cellspacing="0" border="0" class="display" style="font-size:8px;">
					<thead>
						<tr>
							<th width="5%">id</th>
							<th width="20%">title</th>
							<th width="40%">body</th>
							<th width="5%">created_at</th>
							<th width="5%">updated_at</th>
							<th width="10%">tags</th>
							<th width="5%">pic_id</th>
							<th width="5%">is_published</th>
							<th width="5%">num_views</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
					<tfoot>
						<tr>
							<th>id</th>
							<th>title</th>
							<th>body</th>
							<th>created_at</th>
							<th>updated_at</th>
							<th>tags</th>
							<th>pic_id</th>
							<th>is_published</th>
							<th>num_views</th>
						</tr>
					</tfoot>
				</table>
				<br />
				<button id="btn-send-notification-step2-news-send">Send</button>
			</div>
		</div>
		
		<div id="content-send-livepoll">
			<table id="datatable-livepoll" cellpadding="0" cellspacing="0" border="0" class="display" style="font-size:8px;">
				<thead>
					<tr>
						<th width="5%">id</th>
						<th width="25%">title</th>
						<th width="40%">description</th>
						<th width="5%">created_at</th>
						<th width="5%">is_onetime_mode</th>
						<th width="5%">is_private_mode</th>
						<th width="5%">is_published</th>
						<th width="5%">is_open</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
				<tfoot>
					<tr>
						<th>id</th>
						<th>title</th>
						<th>description</th>
						<th>created_at</th>
						<th>is_onetime_mode</th>
						<th>is_private_mode</th>
						<th>is_published</th>
						<th>is_open</th>
					</tr>
				</tfoot>
			</table>
			<br />
			<button id="btn-send-livepoll-step1">Send</button>
		</div>
		
		<div id="content-send-liveslide">
			<div id="tab-send-liveslide-step1">
				<h3>Send LiveSlide: Select Live Session</h3>
				<table id="datatable-livesession" cellpadding="0" cellspacing="0" border="0" class="display" style="font-size:8px;">
					<thead>
						<tr>
							<th width="5%">id</th>
							<th width="30%">title</th>
							<th width="45%">description</th>
							<th width="10%">speaker</th>
							<th width="5%">is_published</th>
							<th width="5%">is_live</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
					<tfoot>
						<tr>
							<th>id</th>
							<th>title</th>
							<th>description</th>
							<th>speaker</th>
							<th>is_published</th>
							<th>is_live</th>
						</tr>
					</tfoot>
				</table>
				<br />
				<button id="btn-send-liveslide-step1-next">Next</button>
			</div>
			<div id="tab-send-liveslide-step2">
				<h3>Send LiveSlide: Select Live Slide</h3>
				<table id="datatable-liveslide" cellpadding="0" cellspacing="0" border="0" class="display" style="font-size: 8px;">
					<thead>
						<tr>
							<th>id</th>
							<th>live_session_id</th>
							<th>title</th>
							<th>res_id</th>
							<th>is_published</th>
							<th>is_presented</th>
							<th>presented_at</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
					<tfoot>
						<tr>
							<th>id</th>
							<th>live_session_id</th>
							<th>title</th>
							<th>res_id</th>
							<th>is_published</th>
							<th>is_presented</th>
							<th>presented_at</th>
						</tr>
					</tfoot>
				</table>
				<br />
				<button id="btn-send-liveslide-step2-back">Back</button>
				<button id="btn-send-liveslide-step2-send">Send</button>
			</div>
		</div>
		
		</div>
		<button id="btn-cancel-popup">Cancel</button>
	</div>
</div>
</div>

</body>
</html>