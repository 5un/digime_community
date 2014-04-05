<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Live_session extends CI_Controller {
	
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
	
	private function _auth_admin(){
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
		return TRUE;
	}
	
	public function index(){
		$live_session_id = $this->input->get('id');
		if($live_session_id){
			$this->db->where('is_published',1);
			$this->db->where('id', $live_session_id);
			$query = $this->db->get('live_session');
			if($query->num_rows()>0){
				$this->output->set_status_header('200');
				$live_session = $query->row_array();
				$result_array = array();
				$result_array['data'] = $live_session;
				echo json_encode($result_array);
				return;
			}else{
				//$this->output->set_status_header('404');
				error_json(ERRORCODE_INVALID_LIVE_SESSION_ID,'The session you are looking for is non-existent.');
				return;
			}
		}
		
		$query_string = $this->input->get('query');
		$older_than_id = $this->input->get('older_than_id');
		$page_size = $this->input->get('size');
		if(!$page_size) $page_size = 20;
		if($query_string==''){
			if($older_than_id>0) $this->db->where('id <', $older_than_id);
			$this->db->limit($page_size);
			$query = $this->db->get('live_session');
			$sessions = $query->result_array();
		}else{
			$wildcarded_query_string = '%'.$this->db->escape_like_str($query_string).'%';
			if($older_than_id >0){
				$sql = "SELECT * FROM live_session WHERE is_published = 1 AND id < ? AND ".
				"(title LIKE ? OR description LIKE ? OR tags LIKE ?) ORDER BY id DESC LIMIT ?";
				$query = $this->db->query($sql, array(intval($older_than_id),
					$wildcarded_query_string,$wildcarded_query_string,$wildcarded_query_string,intval($page_size)));
			}else{
				$sql = "SELECT * FROM live_session WHERE is_published = 1 AND ".
				"(title LIKE ? OR description LIKE ? OR tags LIKE ?) ORDER BY id DESC LIMIT ?";
				$query = $this->db->query($sql, array(
					$wildcarded_query_string,$wildcarded_query_string,$wildcarded_query_string,intval($page_size)));
			}
			$sessions = $query->result_array();
		}
		$this->output->set_status_header('200');
		$result_array['data'] =$sessions;
		echo json_encode($result_array);
	}
	
	public function attend(){
		$user_id = $this->_auth();
		$live_session_id = $this->input->post('live_session_id');
		if(!$live_session_id){
			error_json(ERRORCODE_MISSING_PARAMETER,'missing parameter live_session_id');
			return;
		}
		
		$this->db->trans_start();
		$this->db->where('user_id',$user_id);
		$this->db->where('live_session_id',$live_session_id);
		$query = $this->db->get('live_session_attendance');
		if($query->num_rows()<=0){
			$this->db->where('id',$live_session_id);
			$query = $this->db->get('live_session');
			if($query->num_rows()<0){
				$this->output->set_status_header('404');
				error_json(ERRORCODE_INVALID_LIVE_SESSION_ID,'The session you are trying to attend is non-existent');
				return;
			}
			$this->db->where('id',$live_session_id);
			$this->db->set('num_attendee','num_attendee+1',FALSE);
			$this->db->update('live_session');
			
			$this->db->insert('live_session_attendance',
				array(
					'user_id'=>$user_id,
					'live_session_id'=>$live_session_id
				));
		}
		$this->db->trans_complete();
		if($this->db->trans_status()==FALSE){
			error_json(ERRORCODE_UNKNOWN,'Unknown Error.');
			return;
		}
		echo '{"result":true}';
	}
	
	public function attendance(){
		$user_id = $this->input->get('user_id');
		if(!$user_id){
			error_json(ERRORCODE_MISSING_PARAMETER,'Missing parameter user_id');
			return;
		}
		$live_session_id = $this->input->get('live_session_id');
		if(!$live_session_id){
			error_json(ERRORCODE_MISSING_PARAMETER,'Missing parameter live_session_id');
			return;
		}
		$this->db->where('user_id',$user_id);
		$this->db->where('live_session_id',$live_session_id);
		$query = $this->db->get('live_session_attendance');
		if($query->num_rows()>0) echo '{"result":true}';
		else echo '{"result":false}';
	}
	
	public function attendance_list(){
		$user_id = $this->input->get('user_id');
		if(!$user_id){
			error_json(ERRORCODE_MISSING_PARAMETER,'Missing parameter user_id');
			return;
		}
		$result = array();
		$this->db->select('live_session_id');
		$this->db->where('user_id',$user_id);
		$query = $this->db->get('live_session_attendance');
		$result['data'] = $query->result_array();
		echo json_encode($result);
	}
	
	public function unattend(){
		$user_id = $this->_auth();
		if(!$user_id){
			error_json(ERRORCODE_MISSING_PARAMETER,'Missing parameter user_id');
			return;
		}
		$live_session_id = $this->input->post('live_session_id');
		if(!$live_session_id){
			error_json(ERRORCODE_MISSING_PARAMETER,'Missing parameter live_session_id');
			return;
		}
		
		$this->db->trans_start();
		$this->db->where('user_id',$user_id);
		$this->db->where('live_session_id',$live_session_id);
		$query = $this->db->get('live_session_attendance');
		if($query->num_rows()>0){
			$this->db->where('id',$live_session_id);
			$query = $this->db->get('live_session');
			if($query->num_rows()<0){
				$this->output->set_status_header('404');
				error_json(ERRORCODE_INVALID_LIVE_SESSION_ID,'The session you are trying to attend is non-existent');
				return;
			}
			$this->db->where('id',$live_session_id);
			$this->db->set('num_attendee','num_attendee-1',FALSE);
			$this->db->update('live_session');
		
			$this->db->where('user_id',$user_id);
			$this->db->where('live_session_id',$live_session_id);
			$this->db->delete('live_session_attendance');
		}
		$this->db->trans_complete();
		if($this->db->trans_status()==FALSE){
			error_json(ERRORCODE_UNKNOWN,'Unknown Error.');
			return;
		}
		echo '{"result":true}';
	}
	
	// admin functions ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	
	public function all(){
		
	}
	
	public function add(){
		$admin = $this->_auth_admin();
		$title = $this->input->post('title');
		if(!$title){
			error_json(ERRORCODE_MISSING_PARAMETER,'Missing required paramerter title');
			return;
		}
		$insert_params = array();
		$insert_params['title'] = $title;
		
		$description = $this->input->post('description');
		if($description) $insert_params['description'] = $description;
		$speaker = $this->input->post('speaker');
		if($speaker) $insert_params['speaker'] = $speaker;
		$tags=$this->input->post('tags');
		if($tags) $insert_params['tags'] = $tags;
		$num_attendee = $this->input->post('num_attendee');
		if($num_attendee) $insert_params['num_attendee'] = $num_attendee;
		$is_published = $this->input->post('is_publisjed');
		if($is_published) $insert_params['is_published'] = $is_published;
		
		$result_array = array();
		$success = $this->db->insert('live_session',$insert_params);
		if($success){
			$insert_id = $this->db->insert_id();
			$result_array['id'] = $insert_id;
			echo json_encode($result_array);
			return;
		}else{
			error_json(ERRORCODE_UNKNOWN,"unknown error.");
			return;
		}
	}
	
	public function delete(){
		$admin = $this->_auth_admin();
		$live_session_id = $this->input->post('live_session_id');
		if(!$live_session_id){
			error_json(ERRORCODE_MISSING_PARAMETER,'Missing required parameter live_session_id');
			return;
		}
		$success = $this->db->delete('live_session', array('id'=>$live_session_id));
		if($success){
			echo '{"result":true}';
		}else{
			error_json(ERRORCODE_UNKNOWN,"unknown error.");
		}
	}
	
	public function update(){
		$admin = $this->_auth_admin();
				$live_session_id = $this->input->post('live_session_id');
		if(!$live_session_id){
			error_json(ERRORCODE_MISSING_PARAMETER,'Missing required parameter live_session_id');
			return;
		}
		
		$insert_params = array();
		
		$title = $this->input->post('title');
		if($title) $insert_params['title'] = $title;
		$description = $this->input->post('description');
		if($description) $insert_params['description'] = $description;
		$speaker = $this->input->post('speaker');
		if($speaker) $insert_params['speaker'] = $speaker;
		$tags=$this->input->post('tags');
		if($tags) $insert_params['tags'] = $tags;
		$num_attendee = $this->input->post('num_attendee');
		if($num_attendee) $insert_params['num_attendee'] = $num_attendee;
		$is_published = $this->input->post('is_publisjed');
		if($is_published) $insert_params['is_published'] = $is_published;
		
		$this->db->where('id',$live_session_id);
		$success = $this->db->update('live_session', $insert_params);
		
		if($success) return '{"result":true}';
		else return '{"result":false}';
	}
	
	public function clear(){
		//$this->load->model('LiveSession_model');
		//$result = $this->LiveSession_model->clearAllData();
		echo json_encode($result);
	}
	
	public function settings($action){
		
	}
	
}