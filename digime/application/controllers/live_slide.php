<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Live_slide extends CI_Controller {

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
		$live_slide_id = $this->input->get('id');
		$result_array = array();
		if($live_slide_id){
			$this->db->where('id',$live_slide_id);
			$this->db->where('is_published',1);
			$query = $this->db->get('live_slide');
			if($query->num_rows()>0){
				$live_slide = $query->row_array();
				$result_array['data'] = $live_slide;
				echo json_encode($result_array);
				return;
			}else{
				error_json(ERRORCODE_INVALID_OBJECT_ID,'the live slide you are looking for is non-existent');
				return;
			}
		}else{
			$live_session_id = $this->input->get('live_session_id');
			if(!$live_session_id){
				error_json(ERRORCODE_MISSING_PARAMETER,'missing parameter live_session_id');
				return;
			}
			$older_than_id = $this->input->get('older_than_id');
			$size = $this->input->get('size');
			$this->db->where('live_session_id', $live_session_id);
			$this->db->where('is_published',1);
			$this->db->where('is_presented',1);
			if($older_than_id) $this->db->where('id <',$older_than_id);
			$this->db->order_by('id','DESC');
			if($size) $this->db->limit($size);
			$query = $this->db->get('live_slide');
			$live_slides = $query->result_array();
			$result_array['data'] = $live_slides;
			echo json_encode($result_array);
		}
	}
	
	public function b(){
		$user_id = $this->_auth();
		$live_slide_id = $this->input->post('live_slide_id');
		if(!$live_slide_id){
			error_json(ERRORCODE_MISSING_PARAMETER,'missing parameter live_slide_id');
			return;
		}
		$this->db->where('id',$live_slide_id);
		$query = $this->db->get('live_slide');
		if($query->num_rows()){
			$this->db->where('live_slide_id', $live_slide_id);
			$this->db->where('user_id',$user_id);
			$query = $this->db->get('live_slide_bookmark');
			if($query->num_rows()>0){
				// the user have already bookmarked
				echo '{"result":"true"}';
				return;
			}else{
				// the user have no bookmarked yet
				$success = $this->db->insert('live_slide_bookmark', array(
					'live_slide_id'=>$live_slide_id,
					'user_id'=>$user_id
				));
				if($success){
					echo '{"result":"true"}';
					return;
				}else{
					error_json(ERRORCODE_UNKNOWN,'unknown error');
					return;
				}
			}
		}else{
			error_json(ERRORCODE_INVALID_OBJECT_ID,'the live slide you are looking for is non-existent');
			return;
		}
	}
	
	public function ub(){
		$user_id = $this->_auth();
		$live_slide_id = $this->input->post('live_slide_id');
		if(!$live_slide_id){
			error_json(ERRORCODE_MISSING_PARAMETER,'missing parameter live_slide_id');
			return;
		}
		
		$this->db->where('live_slide_id', $live_slide_id);
		$this->db->where('user_id',$user_id);
		$query = $this->db->get('live_slide_bookmark');
		if($query->num_rows()>0){
			// the user have already bookmarked
			$this->db->where('live_slide_id',$live_slide_id);
			$this->db->where('user_id',$user_id);
			$success = $this->db->delete('live_slide_bookmark');
			if($success){
				echo '{"result":"true"}';
				return;
			}else{
				error_json(ERRORCODE_UNKNOWN,'unknown error');
				return;
			}
		}else{
			// the user have no bookmarked yet
			echo '{"result":"true"}';
			return;
		}
	}
	
	public function user_bookmarks(){
		$user_id = $this->_auth();
		$result_array = array();
		$this->db->select('live_slide_id');
		$this->db->where('user_id',$user_id);
		$query = $this->db->get('live_slide_bookmark');
		$bookmarks = $query->result_array();
		$result_array['data'] = $bookmarks;
		echo json_encode($result_array);
	}
}