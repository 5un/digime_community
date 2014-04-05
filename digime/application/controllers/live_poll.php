<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Live_poll extends CI_Controller {

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

	// get all the polls
	public function index(){
		$poll_id = $this->input->get('id');
		if($poll_id){			
			$this->db->where('is_published',1);
			$this->db->where('id', $poll_id);
			$query = $this->db->get('live_poll');
			$result_a = array();
			if($query->num_rows()>0){
				$poll_array = $query->result_array();
				$result_a['poll'] = $poll_array[0];
				$this->db->where('live_poll_id', $poll_id);
				$query = $this->db->get('live_poll_answer');
				$result_a['ans'] = $query->result_array();
			}else{
				error_json(ERRORCODE_INVALID_LIVE_POLL_ID,'The poll you are looking for is non-existent.');
				return;
			}
			
			$result['data'] = $result_a;
			echo json_encode($result);
		}else{
			$older_than_id = $this->input->get('older_than_id');
			$page_size = $this->input->get('size');
			if(!$page_size) $page_size = 20;

			if($older_than_id>0){
				$this->db->where('id <', $older_than_id);
				$this->db->where('is_published',1);
				$this->db->limit($page_size);
				$this->db->order_by('id','DESC');
				$query = $this->db->get('live_poll');
				$polls = $query->result_array();
			}else{
				$this->db->limit($page_size);
				$this->db->where('is_published',1);
				$query = $this->db->get('live_poll');
				$this->db->order_by('id','DESC');
				$polls = $query->result_array();
			}
			$result = array();
			$result['data'] = $polls;
			echo json_encode($result);
		}
	}
	
	// will be deprecated
	// get poll with id
	public function p(){
		$poll_id = $this->input->get('poll_id');
		$this->load->model('LivePoll_model');
		$poll = $this->LivePoll_model->getPollWithID($poll_id);
		if(!$poll){
			error_json(ERRORCODE_INVALID_LIVE_POLL_ID,'The poll you are looking for is non-existent.');
			return;
		}
		$result['result'] = TRUE;
		$result['data'] = $poll;
		echo json_encode($result);
	}
	
	// get poll with id and user vote
	public function pu(){
		$user_id = $this->_auth();
		$poll_id = $this->input->get('poll_id');
		$this->load->model('LivePoll_model');
		$poll = $this->LivePoll_model->getPollAndUserVote($poll_id,$user_id);
		if(!$poll){
			error_json(ERRORCODE_INVALID_LIVE_POLL_ID,'The poll you are looking for is non-existent.');
			return;
		}
		$result['data'] = $poll;
		echo json_encode($result);
	}
	
	// refresh poll
	public function pr(){
		$user_id = $this->_auth();
		$poll_id = $this->input->get('poll_id');
		$this->load->model('LivePoll_model');
		$poll = $this->LivePoll_model->getPollRefresh($poll_id,$user_id);
		$result['result'] = TRUE;
		$result['data'] = $poll;
		echo json_encode($result);
	}
	
	// vote in a poll
	public function v(){
		$user_id = $this->_auth();
		$poll_id = $this->input->post('poll_id');
		$ans_id = $this->input->post('ans_id');
		$result_array = array();
		$this->load->model('LivePoll_model');
		$result_array['result'] = $this->LivePoll_model->vote3($user_id,$poll_id,$ans_id);
		echo json_encode($result_array);
	}
	
	//unvote
	public function uv(){
		$user_id = $this->_auth();
		$poll_id = $this->input->post('poll_id');
		$result_array = array();
		$this->load->model('LivePoll_model');
		$result_array['result'] = $this->LivePoll_model->unvote($user_id,$poll_id);
		echo json_encode($result_array);
	}

}