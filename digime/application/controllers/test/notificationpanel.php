<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class NotificationPanel extends CI_Controller {
	
	public function index(){
		$this->load->view('admin/notification');
	}
	
	public function send_announcement(){
		
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
			echo 'sending...'.json_encode($slide);
			$this->load->library('ejabberd_xmlrpc',array('url'=>'http://127.0.0.1:4560'));
			$this->ejabberd_xmlrpc->send_liveslide_users($users_jabber, json_encode($slide));
			echo 'success';
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
		$poll = $this->LivePoll_model->getPollWithID($live_poll_id);
		if($poll){
			echo 'sending...'.json_encode($poll);
			$this->load->library('ejabberd_xmlrpc',array('url'=>'http://127.0.0.1:4560'));
			$this->ejabberd_xmlrpc->send_livepoll_users($users_jabber, json_encode($poll));
			echo 'success';
		}else{
			echo 'error: poll not exists';
		}
	}
	
}