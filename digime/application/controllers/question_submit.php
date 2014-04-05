<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Question_submit extends CI_Controller {
	
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
		$question = $this->input->post('q');
		if(!$question){
			error_json(ERRORCODE_MISSING_PARAMETER,'missing parameter q');
			return;
		}
		
		$success = $this->db->insert('ext_questions',array(
			'user_id' => $user_id,
			'question'=>$question
		));
		
		if($success){
			$result_array = array();
			$result_array['result']=true;
		}else{
			$result_array = array();
			$result_array['result']=false;
		}
		echo json_encode($result_array);
		return;
	}
	
}