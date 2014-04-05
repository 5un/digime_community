<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Notificationpanel extends CI_Controller {
	
	
	public function index(){
		$view_data = array();
		
		$this->db->order_by('id','DESC');
		$this->db->limit(10);
		$query = $this->db->get('announcement');
		$announcements = $query->result();
		$view_data['announcements'] = $announcements;
		
		$this->db->limit(10);
		$query = $this->db->get('notification');
		$notifications = $query->result();
		$view_data['notifications'] = $notifications;
		
		$view_data['ejabberd_status'] = 'online';
		$this->load->library('ejabberd_xmlrpc',array('url'=>$this->config->item('ejabberd_xmlrpc_url')));
		$view_data['ejabberd_default_service_url'] = $this->config->item('ejabberd_xmlrpc_url');
		$response = $this->ejabberd_xmlrpc->status();
		if(!isset($response['res'])||$response['res']!=0){
			$view_data['ejabberd_status'] = 'ejabberd is not running or cannot be reached';
			$view_data['ejabberd_status_ok'] = false;
		}else{
			$view_data['ejabberd_status'] = $response['text'];
			$view_data['ejabberd_status_ok'] = true;
		}
		
		$this->load->view('admin/notificationcenter',$view_data);
	}
	
	public function send_announcement(){
		$title = $this->input->post('announcement_title');
		$body = $this->input->post('announcement_body');
		
		$query = $this->db->get('user');
		$users = $query->result();
		$users_jabber = array();
		$ejabberd_host = $this->config->item('ejabberd_host');
		$i=0;
		foreach($users as $user){
			$users_jabber[$i]=$user->username.'@'.$ejabberd_host ;
			$i++;
		}
		
		$this->db->insert('announcement', array(
			'title'=>$title,
			'body'=>$body
		));
		$insert_id = $this->db->insert_id();
		$this->load->library('ejabberd_xmlrpc',array('url'=>'http://127.0.0.1:4560'));
		$response = $this->ejabberd_xmlrpc->send_announcement_users($users_jabber,$insert_id,$title,$body);
		
		if(!isset($response['res'])||$response['res']!=0){
			echo 'failure';
		}else{
			echo 'success';
		}
	}
	
	public function send_news_notification(){
		// get news and build notification message
		$news_id = $this->input->post('news_id');
		$this->db->where('id', $news_id);
		$query = $this->db->get('news');
		if($query->num_rows()<=0){
			echo 'error: news not exists';
			return;
		}
		$news = $query->row();	
		$notification_message = 'New news added <a href="digime://sg.com.digimagic.android.digime/news/'.
								$news_id.'">'.$news->title.
								'</a>.';
		$this->db->insert('ejabberd_command_log', array(
			'type'=>1,
			'command'=>'notification for news ' . $news_id,
			'result'=>'pending'
		));
		$ejabberd_command_log_id = $this->db->insert_id();
		
		// get users
		$query = $this->db->get('user');
		$users = $query->result();
		$users_jabber = array();
		$ejabberd_host = $this->config->item('ejabberd_host');
		
		$i=0;
		foreach($users as $user){
			$users_jabber[$i]=$user->username.'@'.$ejabberd_host ;
			$i++;
			
			$insert_data = array();
			$insert_data['id'] = $ejabberd_command_log_id;
			$insert_data['user_id'] = $user->id;
			$insert_data['message'] = $notification_message;
			$insert_date['type'] = 1;
			$this->db->insert('notification', $insert_data);
		}
		
		// payload
		$payload_array = array();
		$payload_array['id'] = $ejabberd_command_log_id;
		$payload_array['message'] = $notification_message;
		$payload_array['created_at'] = date('Y-m-d H-i-s');
		$payload_array['type'] = 1;
		$payload = json_encode($payload_array);
		
		// send the message
		$xmlrpc_url = 'http://127.0.0.1:4560';
		$this->load->library('ejabberd_xmlrpc', array('url'=>$xmlrpc_url));
		$response = $this->ejabberd_xmlrpc->send_notification_users($users_jabber,$payload);
		
		if(!isset($response['res'])||$response['res']!=0){
			$this->db->where('id', $ejabberd_command_log_id);
			$this->db->update('ejabberd_command_log', array('result'=>'failure'));
			echo 'failure';
		}else{
			$this->db->where('id', $ejabberd_command_log_id);
			$this->db->update('ejabberd_command_log', array('result'=>'success'));
			echo 'success';
		}
		
	}
	
	public function send_livesession_notification(){
		// get news and build notification message
		$live_session_id = $this->input->post('live_session_id');
		$this->db->where('id', $live_session_id);
		$query = $this->db->get('live_session');
		if($query->num_rows()<=0){
			echo 'error: live_session not exists';
			return;
		}
		$live_session = $query->row();	
		$notification_message = 'Session <a href="digime://sg.com.digimagic.android.digime/live_session/'.
								$live_session_id.'">'.$live_session->title.
								'</a> has became live.';
		$this->db->insert('ejabberd_command_log', array(
			'type'=>1,
			'command'=>'notification for live session ' . $live_session_id,
			'result'=>'pending'
		));
		$ejabberd_command_log_id = $this->db->insert_id();
		
		// get users
		$query = $this->db->get('user');
		$users = $query->result();
		$users_jabber = array();
		$ejabberd_host = $this->config->item('ejabberd_host');
		
		$i=0;
		foreach($users as $user){
			$users_jabber[$i]=$user->username.'@'.$ejabberd_host ;
			$i++;
			
			$insert_data = array();
			$insert_data['id'] = $ejabberd_command_log_id;
			$insert_data['user_id'] = $user->id;
			$insert_data['message'] = $notification_message;
			$insert_date['type'] = 2;
			$this->db->insert('notification', $insert_data);
		}
		
		// payload
		$payload_array = array();
		$payload_array['id'] = $ejabberd_command_log_id;
		$payload_array['message'] = $notification_message;
		$payload_array['created_at'] = date('Y-m-d H-i-s');
		$payload_array['type'] = 2;
		$payload = json_encode($payload_array);
		
		// send the message
		$xmlrpc_url = 'http://127.0.0.1:4560';
		$this->load->library('ejabberd_xmlrpc', array('url'=>$xmlrpc_url));
		$response = $this->ejabberd_xmlrpc->send_notification_users($users_jabber,$payload);
		
		if(!isset($response['res'])||$response['res']!=0){
			$this->db->where('id', $ejabberd_command_log_id);
			$this->db->update('ejabberd_command_log', array('result'=>'failure'));
			echo 'failure';
		}else{
			$this->db->where('id', $ejabberd_command_log_id);
			$this->db->update('ejabberd_command_log', array('result'=>'success'));
			echo 'success';
		}
	}
	
	public function send_liveslide(){
		$live_slide_id = $this->input->post('id');
		$query = $this->db->get('user');
		$users = $query->result();
		$users_jabber = array();
		
		$ejabberd_host = $this->config->item('ejabberd_host');
		$i=0;
		foreach($users as $user){
			$users_jabber[$i]=$user->username.'@'.$ejabberd_host ;
			$i++;
		}

		$this->db->where('id',$live_slide_id);
		$query = $this->db->get('live_slide');
		$slide = $query->row_array();
		
		if($slide){
			//echo 'sending...'.json_encode($slide);
			$this->load->library('ejabberd_xmlrpc',array('url'=>'http://127.0.0.1:4560'));
			$response = $this->ejabberd_xmlrpc->send_liveslide_users($users_jabber, json_encode($slide));
			if(!isset($response['res'])||$response['res']!=0){
				$this->db->insert('ejabberd_command_log', array(
					'type' => 4,
					'command' => 'live_slide '.$live_slide_id,
					'result' => 'failure'
				));
				echo 'failure';
			}else{
				$this->db->insert('ejabberd_command_log', array(
					'type' => 4,
					'command' => 'live_slide '.$live_slide_id,
					'result' => 'success'
				));
				echo 'success';
			}
			$this->db->where('id', $live_slide_id);
			$this->db->update('live_slide', array('is_presented'=>1));
		}else{
			echo 'error: slide not exists';
		}
	}
	
	public function send_livepoll(){
		$live_poll_id = $this->input->post('id');
		$query = $this->db->get('user');
		$users = $query->result();
		$users_jabber = array();
		
		$ejabberd_host = $this->config->item('ejabberd_host');
		$i=0;
		foreach($users as $user){
			$users_jabber[$i]=$user->username.'@'.$ejabberd_host ;
			$i++;
		}
		
		$this->load->model('LivePoll_model');
		
		$this->db->where('is_published',1);
		$this->db->where('id', $live_poll_id);
		$query = $this->db->get('live_poll');
		$result_a = array();
		if($query->num_rows()>0){
			$poll_array = $query->result_array();
			$result_a['poll'] = $poll_array[0];
			$this->db->where('live_poll_id', $live_poll_id);
			$query = $this->db->get('live_poll_answer');
			$result_a['ans'] = $query->result_array();
		}else{
			error_json(ERRORCODE_INVALID_LIVE_POLL_ID,'The poll you are looking for is non-existent.');
			return;
		}
		
		$poll = $result_a;
		if($poll){
			echo 'sending...'.json_encode($poll);
			$this->load->library('ejabberd_xmlrpc',array('url'=>'http://127.0.0.1:4560'));
			$response = $this->ejabberd_xmlrpc->send_livepoll_users($users_jabber, json_encode($poll));
			if(!isset($response['res'])||$response['res']!=0){
				echo 'failure';
				$this->db->insert('ejabberd_command_log', array(
					'type' => 3,
					'command' => 'live_poll '.$live_poll_id,
					'result' => 'failure'
				));
			}else{
				$this->db->insert('ejabberd_command_log', array(
					'type' => 3,
					'command' => 'live_poll '.$live_poll_id,
					'result' => 'success'
				));
				echo 'success';
			}
			
		}else{
			echo 'error: poll not exists';
		}
	}
	
	// data backend functions
	
	public function get_announcements(){
		$older_than_id = $this->input->get('older_than_id');
		$size = $this->input->get('size');
		if(!$size) $size = 10;
		
		if($older_than_id) $this->db->where('id <', $older_than_id);
		$this->db->limit($size);
		$this->db->order_by('id','DESC');
		
		$query = $this->db->get('announcement');
		$result['data'] = $query->result_array();
		
		echo json_encode($result);
	}
	
	public function livesession_datatable(){
		$result_array = array();
		$result_array['sEcho'] = intval($this->input->get('sEcho'));
		
		$this->db->select("id, title, description, speaker, pic_id, tags, num_attendee, is_published, is_live, CONCAT('live_session_row', id ) AS DT_RowId");
		
		$sSearch = $this->input->get('sSearch');
		if($sSearch){
			$this->db->like('title', $sSearch);
			$this->db->or_like('description', $sSearch);
			$this->db->or_like('tags', $sSearch);
		}
		
		$iDisplayStart = $this->input->get('iDisplayStart');
		$iDisplayLength = $this->input->get('iDisplayLength');
		
		if($iDisplayStart){
			$this->db->limit($iDisplayStart, $iDisplayLength);
		}
		
		$query = $this->db->get('live_session');
		
		$result_array['iTotalRecords'] = $query->num_rows();
		$result_array['iTotalDisplayRecords'] = $query->num_rows();
		
		$result_array['aaData'] = $query->result_array();
		
		echo json_encode($result_array);
	}
	
	public function news_datatable(){
		$result_array = array();
		$result_array['sEcho'] = intval($this->input->get('sEcho'));
		
		$this->db->select("id, title, body, created_at, updated_at, tags, pic_id, is_published, num_views, CONCAT('news_row', id ) AS DT_RowId");
		
		$sSearch = $this->input->get('sSearch');
		if($sSearch){
			$this->db->like('title', $sSearch);
			$this->db->or_like('body', $sSearch);
			$this->db->or_like('tags', $sSearch);
		}
		
		$iDisplayStart = $this->input->get('iDisplayStart');
		$iDisplayLength = $this->input->get('iDisplayLength');
		
		if($iDisplayStart){
			$this->db->limit($iDisplayStart, $iDisplayLength);
		}
		
		$query = $this->db->get('news');
		
		$result_array['iTotalRecords'] = $query->num_rows();
		$result_array['iTotalDisplayRecords'] = $query->num_rows();
		
		$result_array['aaData'] = $query->result_array();
		
		echo json_encode($result_array);
	}
	
	public function liveslide_datatable(){
		$result_array = array();
		$result_array['sEcho'] = intval($this->input->get('sEcho'));
		
		$live_session_id = $this->input->get('live_session_id');
		
		$this->db->where('live_session_id', $live_session_id);
		$query1 = $this->db->get('live_slide');
		$num_total_rows = $query1->num_rows();
	
		$this->db->select("id, live_session_id, title, res_id, is_published, is_presented ,presented_at, CONCAT('liveslide_row', id ) AS DT_RowId");
		
		$this->db->where('live_session_id', $live_session_id);
		
		$iDisplayStart = $this->input->get('iDisplayStart');
		$iDisplayLength = $this->input->get('iDisplayLength');
		$this->db->limit($iDisplayLength,$iDisplayStart);
		$this->db->order_by('id','DESC');
		
		$query = $this->db->get('live_slide');
		
		$result_array['iTotalRecords'] = $num_total_rows;
		$result_array['iTotalDisplayRecords'] = $num_total_rows;
		
		$result_array['aaData'] = $query->result_array();
		
		echo json_encode($result_array);
	}
	
	public function livepoll_datatable(){
		$result_array = array();
		$result_array['sEcho'] = intval($this->input->get('sEcho'));
		
		$query1 = $this->db->get('live_poll');
		$num_total_rows = $query1->num_rows();
		
		$this->db->select("id, title, description, created_at, last_vote_at, is_onetime_mode, is_private_mode, is_published, is_open, CONCAT('live_poll_row', id ) AS DT_RowId");
		
		$iDisplayStart = $this->input->get('iDisplayStart');
		$iDisplayLength = $this->input->get('iDisplayLength');
		$this->db->limit($iDisplayLength,$iDisplayStart);
		$this->db->order_by('id','DESC');
		
		$query = $this->db->get('live_poll');
		
		$result_array['iTotalRecords'] = $num_total_rows;
		$result_array['iTotalDisplayRecords'] = $num_total_rows;
		
		$result_array['aaData'] = $query->result_array();
		
		echo json_encode($result_array);
	}
	
	public function announcement_datatable(){
		$result_array = array();
		$result_array['sEcho'] = intval($this->input->get('sEcho'));
		
		$query1 = $this->db->get('announcement');
		$num_total_rows = $query1->num_rows();
	
		$this->db->select("id, title, body, created_at, CONCAT('announcement_row', id ) AS DT_RowId");
		
		$iDisplayStart = $this->input->get('iDisplayStart');
		$iDisplayLength = $this->input->get('iDisplayLength');
		$this->db->limit($iDisplayLength,$iDisplayStart);
		$this->db->order_by('id','DESC');
		
		$query = $this->db->get('announcement');
		
		$result_array['iTotalRecords'] = $num_total_rows;
		$result_array['iTotalDisplayRecords'] = $num_total_rows;
		
		$result_array['aaData'] = $query->result_array();
		
		echo json_encode($result_array);
	}
	
	public function notification_history_datatable(){
		$result_array = array();
		$result_array['sEcho'] = intval($this->input->get('sEcho'));
		
		$query1 = $this->db->get('notification');
		$num_total_rows = $query1->num_rows();
		
		$this->db->select("id, user_id, message, created_at, is_read, type");
		
		$iDisplayStart = $this->input->get('iDisplayStart');
		$iDisplayLength = $this->input->get('iDisplayLength');
		$this->db->limit($iDisplayLength,$iDisplayStart);
		$this->db->order_by('id','DESC');
		
		$query = $this->db->get('notification');
		$result_array['iTotalRecords'] = $num_total_rows;
		$result_array['iTotalDisplayRecords'] = $num_total_rows;
		
		$result_array['aaData'] = $query->result_array();
		
		echo json_encode($result_array);
	}
	
	public function livepoll_history_datatable(){
		$result_array = array();
		$result_array['sEcho'] = intval($this->input->get('sEcho'));
		
		$this->db->where('type', 3);
		$query1 = $this->db->get('ejabberd_command_log');
		$num_total_rows = $query1->num_rows();
		
		$this->db->select("command, result, created_at");
		
		$iDisplayStart = $this->input->get('iDisplayStart');
		$iDisplayLength = $this->input->get('iDisplayLength');
		$this->db->limit($iDisplayLength,$iDisplayStart);
		$this->db->order_by('id','DESC');
		
		$this->db->where('type', 3);
		$query = $this->db->get('ejabberd_command_log');
		$result_array['iTotalRecords'] = $num_total_rows;
		$result_array['iTotalDisplayRecords'] = $num_total_rows;
		
		$result_array['aaData'] = $query->result_array();
		
		echo json_encode($result_array);
	}
	
	public function liveslide_history_datatable(){
		$result_array = array();
		$result_array['sEcho'] = intval($this->input->get('sEcho'));
		
		$this->db->where('type', 4);
		$query1 = $this->db->get('ejabberd_command_log');
		$num_total_rows = $query1->num_rows();
		
		$this->db->select("command, result, created_at");
		
		$iDisplayStart = $this->input->get('iDisplayStart');
		$iDisplayLength = $this->input->get('iDisplayLength');
		$this->db->limit($iDisplayLength,$iDisplayStart);
		$this->db->order_by('id','DESC');
		
		$this->db->where('type', 4);
		$query = $this->db->get('ejabberd_command_log');
		$result_array['iTotalRecords'] = $num_total_rows;
		$result_array['iTotalDisplayRecords'] = $num_total_rows;
		
		$result_array['aaData'] = $query->result_array();
		
		echo json_encode($result_array);
	}
	
	function testFire(){
		$this->load->view('admin/testfire');
	}
	
	function testFire_random(){
		$this->load->view('admin/testfire_random');
	}
}