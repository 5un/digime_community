<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Notification extends CI_Controller {
	
	public function index(){
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
		$this->load->view('admin/notification',$view_data);
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
	
	public function send_notification(){
		
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
			$response $this->ejabberd_xmlrpc->send_liveslide_users($users_jabber, json_encode($slide));
			if(!isset($response['res'])||$response['res']!=0){
				echo 'failure';
			}else{
				echo 'success';
			}
		}
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
			}else{
				echo 'success';
			}
			
		}else{
			echo 'error: poll not exists';
		}
	}
	
}