<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Notification extends CI_Controller {

	private function _auth(){
		$access_token = $this->input->get('access_token');
		if(!$access_token){
			$access_token = $this->input->post('access_token');
			if(!$access_token){
				error_json(ERRORCODE_MISSING_ACCESS_TOKEN, 'missing parameter access_token');
				exit;
			}
		}
		$this->load->model('Auth_model');
		$oauth_session = $this->Auth_model->verifyAccessToken($access_token);
		if(!$oauth_session){
			error_json(ERRORCODE_INVALID_ACCESS_TOKEN, 'invalid access_token');
			exit;
		}
		return $oauth_session->user_id;
	}

	public function index(){
		$user_id = $this->_auth();
		$older_than_id = $this->input->get('older_than_id');
		$size = $this->input->get('size');
		if(!$size) $size = 10;
		
		$this->db->where('user_id', $user_id);
		if($older_than_id) $this->db->where('id <', $older_than_id);
		$this->db->order_by('id','DESC');
		$this->db->limit($size);
		$query = $this->db->get('notification');	

		$result_array = array();
		$result_array['data'] = $query->result_array();
		echo json_encode($result_array);
		
	}
	
	public function num_unread(){
		$user_id = $this->_auth();
		$this->db->where('user_id', $user_id);
		$this->db->where('is_read',0);
		$query = $this->db->get('notification');
		$num_unread = $query->num_rows();
		$result_array['data'] = $num_unread;
		echo json_encode($result_array);
	}

		
	public function	markread(){
		$user_id = $this->_auth();
		$from_id = $this->input->post('from_id');
		if(!$from_id){
			error_json(ERRORCODE_MISSING_PARAMETER,'missing parameter from_id');
			return;
		}

		$this->db->where('user_id', $user_id);
		$this->db->where('id <=', $from_id);
		$this->db->where('is_read', 0);
		$success = $this->db->update('notification', array(
			'is_read' => 1
		));
		
		if($success){
			echo '{"result":"true"}';
		}else{
			echo '{"result":"false"}';
		}
		
	}
	
}