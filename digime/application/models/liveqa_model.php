<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class LiveQA_model extends CI_Model {

	public function getTopQuestionsForSession($live_session_id, $offset, $page_size){
		$this->db->where('live_session_id',$live_session_id);
		$this->db->order_by('up_votes','DESC');
		$this->db->limit($page_size,$offset);
		$query = $this->db->get('live_qa');
		return $query->result_array();
	}
	
	public function searchQuestionForSession($live_seesion_id, $keyword, $offset, $page_size){
		$this->db->where('live_session_id', $live_session_id);
		$this->db->like('title',$keyword);
		$this->db->order_by('up_votes','DESC');
		$this->db->limit($page_size,$offset);
		$query = $this->db->get('live_qa');
		return $query->result_array();
	}
	
	public function addQuestion($session_id, $title, $user_id){
		$success = $this->db->insert('live_qa', array(
			'live_session_id' => $session_id,
			'title' => $title,
			'user_id' => $user_id,
			'answer' => ' '
		));
		if($success){
			$question_id = $this->db->insert_id();
			$this->db->where('id',$question_id);
			$query = $this->db->get('live_qa');
			return $query->row_array();
		}else 
			return FALSE;
	}
	
	public function addQuestionAnnonymous($session_id, $title){
		$this->addQuestion($session_id,$title,0);
	}
	
	public function getUpvotesOfUser($user_id){
		$this->db->select('live_qa_id');
		$this->db->where('user_id',$user_id);
		$query = $this->db->get('live_qa_vote');
		return  $query->result_array();
	}
	
	public function upvote($user_id, $question_id){
		$this->db->trans_start();
			$this->db->where('live_qa_id',$question_id);
			$this->db->where('user_id',$user_id);
			$query = $this->db->get('live_qa_vote');
			if($query->num_rows()>0){
				// the user has upvoted
				
			}else{
				// the user has not upvoted
				$this->db->set('up_votes', 'up_votes+1', FALSE);
				$this->db->where('id',$question_id);
				$this->db->update('live_qa');
				
				$this->db->insert('live_qa_vote',array(
					'live_qa_id' => $question_id,
					'user_id' => $user_id
				));
			}
		$this->db->trans_complete();
		if($this->db->trans_status()==FALSE) return FALSE;
		return TRUE;
	}
	
	public function unupvote($user_id, $question_id){
		$this->db->trans_start();
			$this->db->where('live_qa_id', $question_id);
			$this->db->where('user_id',$user_id);
			$query = $this->db->get('live_qa_vote');
			if($query->num_rows()>0){
				// user has voted
				$this->db->set('up_votes','up_votes-1', FALSE);
				$this->db->where('id',$question_id);
				$this->db->update('live_qa');
				
				$this->db->where('live_qa_id', $question_id);
				$this->db->where('user_id', $user_id);
				$this->db->delete('live_qa_vote');
			}else{
				// the user has not upvoted
			}
		$this->db->trans_complete();
		if($this->db->trans_status()==FALSE) return FALSE;
		return TRUE;
	}
	
	public function clearVoteForQuestion($question_id){
		$this->db->trans_start();
			$this->db->where('id', $question_id);
			$this->db->update('live_qa', array('up_votes' => 0));
		
			$this->db->where('live_qa_id', $question_id);
			$this->db->delete('live_qa_vote');
		$this->db->trans_complete();
		if($this->db->trans_status()==FALSE) return FALSE;
		return TRUE;
	}

}