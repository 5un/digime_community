<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Live_qa extends CI_Controller {

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
		$live_session_id = $this->input->get('live_session_id');
		$offset = $this->input->get('offset');
		$page_size = $this->input->get('page_size');
		if(!$page_size) $page_size = 20;
		$this->load->model('LiveQA_model');
		$questions = $this->LiveQA_model->getTopQuestionsForSession($live_session_id,$offset,$page_size);
		$response = array();
		$response['data'] = $questions;
		echo json_encode($response);
	}

	// get top questions
	public function g(){
		$live_session_id = $this->input->get('live_session_id');
		$offset = $this->input->get('offset');
		$page_size = $this->input->get('page_size');
		$this->load->model('LiveQA_model');
		$questions = $this->LiveQA_model->getTopQuestionsForSession($live_session_id,$offset,$page_size);
		$response = array();
		$response['result'] = true;
		$response['data'] = $questions;
		echo json_encode($response);
	}
	
	// vote for a question
	public function v(){
		$user_id = $this->_auth();
		$question_id = $this->input->post('question_id');
		$result_array = array();
		$this->load->model('LiveQA_model');
		$result_array['result'] = $this->LiveQA_model->upvote($user_id,$question_id);
		echo json_encode($result_array);
	}
	
	// unvote a question
	public function uv(){
		$user_id = $this->_auth();
		$question_id = $this->input->post('question_id');
		$result_array = array();
		$this->load->model('LiveQA_model');
		$result_array['result'] = $this->LiveQA_model->unupvote($user_id,$question_id);
		echo json_encode($result_array);
	}
	
	//get
	public function user_upvotes(){
		$user_id = $this->_auth();
		$this->load->model('LiveQA_model');
		$result = array();
		$result['data'] = $this->LiveQA_model->getUpvotesOfUser($user_id);
		echo json_encode($result);
	}
	
	// add a question
	public function aq(){
		$user_id = $this->_auth();
		$live_session_id = $this->input->post('live_session_id');
		$question = $this->input->post('question');
		// TODO put state to unpublished
		$this->load->model('LiveQA_model');
		$new_question = $this->LiveQA_model->addQuestion($live_session_id, $question, $user_id);
		$result = array();
		if($new_question){
			$result['result'] = TRUE;
			$result['data'] = $new_question;
			echo json_encode($result);
		}else{
			$result['result'] = FALSE;
			// TODO update error
			$result['error'] = 'cannot insert';
			echo json_encode($result);
		}
	}
	
	public function cv(){
		$question_id = $this->input->post('question_id');
		$this->load->model('LiveQA_model');
		$result_array = array();
		$result_array['result'] = $this->LiveQA_model->clearVoteForQuestion($question_id);
		echo json_encode($result_array);
	}
	// admin functions 
	

}