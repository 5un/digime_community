<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class LivePoll_model extends CI_Model {
	
	public function getAllPollPaged($offset,$page_size){
		$this->db->limit($page_size,$offset);
		$query = $this->db->get('live_poll');
		return $query->result_array();
	}
	
	public function getAllPollOlderThan($older_than_id,$page_size){
		$this->db->where('id <', $older_than_id);
		$this->db->limit($page_size);
		$query = $this->db->get('live_poll');
		return $query->result_array();
	}
	
	public function getPollWithID($poll_id){
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
			$result_a['poll'] = NULL;
			$result_a['ans'] = NULL;
		}
		return $result_a;
	}
	
	public function getPollAndUserVote($poll_id,$user_id){
		$this->db->where('id', $poll_id);
		$query = $this->db->get('live_poll');
		$result_a = array();
		if($query->num_rows()>0){
			$poll_array = $query->result_array();
			$result_a['poll'] = $poll_array[0];
			$this->db->where('live_poll_id', $poll_id);
			$query = $this->db->get('live_poll_answer');
			$result_a['ans'] = $query->result_array();
			
			$this->db->where('live_poll_id',$poll_id);
			$this->db->where('user_id',$user_id);
			$query = $this->db->get('live_poll_vote');
			if($query->num_rows()>0){
				$vote_array = $query->result_array();
				$result_a['vote'] = $vote_array[0];
			}else{
				$result_a['vote'] = NULL;
			}
		}else{
			$result_a['poll'] = NULL;
			$result_a['ans'] = NULL;
			$result_a['vote']=NULL;
			return false;
		}
		return $result_a;
	}
	
	public function getPollRefresh($poll_id,$user_id){
		$this->db->where('id', $poll_id);
		$query = $this->db->get('live_poll');
		$result_a = array();
		if($query->num_rows()>0){
			$poll_array = $query->result_array();
			$this->db->select('id,num_votes');
			$this->db->where('live_poll_id', $poll_id);
			$query = $this->db->get('live_poll_answer');
			$result_a['ans'] = $query->result_array();
			
			$this->db->where('live_poll_id',$poll_id);
			$this->db->where('user_id',$user_id);
			$query = $this->db->get('live_poll_vote');
			if($query->num_rows()>0){
				$vote_array = $query->result_array();
				$result_a['vote'] = $vote_array[0];
			}else{
				$result_a['vote'] = NULL;
			}
		}else{
			$result_a['ans'] = NULL;
			$result_a['vote']=NULL;
		}
		return $result_a;
	}
	
	public function createPoll($title){
		$success = $this->db->insert('live_poll', array('title' => $title));
		if($success) return $this->db->insert_id();
		else return FALSE;
	}
	
	public function addAnswerToPoll($poll_id,$ans_title){
		$this->db->insert('live_poll_answer', 
			array(
				'live_poll_id' => $poll_id,
				'title' => $ans_title,
				'num_votes' => 0
		));
	}
	
	public function deleteAnswerFromPoll($ans_id){
		$this->db->where('id',$ans_id);
		$this->db->delete('live_poll_answer');
	}
	
	public function deletePollwithID($poll_id){
		$this->db->where('id',$poll_id);
		$this->db->delete('live_poll');
	}
	
	public function vote($user_id, $poll_id, $ans_id){
		$this->db->where('user_id', $user_id);
		$this->db->where('live_poll_id',$poll_id);
		$query = $this->db->get('live_poll_vote');
		if($query->num_rows()>0){
			// the user have already voted
			$vote = $query->row();
			
			$this->db->trans_start();
			
			$this->db->set('num_votes','num_votes-1',FALSE);
			$this->db->where('id',$vote->live_poll_answer_id);
			$this->db->update('live_poll_answer');
			
			$this->db->set('num_votes','num_votes+1',FALSE);
			$this->db->where('id',$ans_id);
			$this->db->update('live_poll_answer');
			
			$this->db->where('id',$vote->id);
			$this->db->update('live_poll_vote', array(
				'live_poll_answer_id' => $ans_id
			));
			
			$this->db->trans_complete();
			if($this->db->trans_status()==FALSE) return FALSE;
			return TRUE;
		}else{
			// the user have not voted
			$this->db->trans_start();
			
			$this->db->set('num_votes','num_votes+1',FALSE);
			$this->db->where('id',$ans_id);
			$this->db->update('live_poll_answer');
			
			$this->db->insert('live_poll_vote',array(
				'live_poll_id' => $poll_id,
				'live_poll_answer_id' => $ans_id,
				'user_id' => $user_id
			));
			$this->db->trans_complete();
			
			if($this->db->trans_status()==FALSE) return FALSE;
			return TRUE;
		}
	}
	
	public function vote2($user_id, $poll_id,$ans_id){
		$this->db->trans_start();
		
		$query = $this->db->query('SELECT * FROM live_poll_vote WHERE user_id='.$user_id.' AND live_poll_id='.$poll_id.' LOCK IN SHARE MODE');
		if($query->num_rows()>0){
			// the user have already voted
			$vote = $query->row();
			
			$this->db->set('num_votes','num_votes-1',FALSE);
			$this->db->where('id',$vote->live_poll_answer_id);
			$this->db->update('live_poll_answer');
			
			$this->db->set('num_votes','num_votes+1',FALSE);
			$this->db->where('id',$ans_id);
			$this->db->update('live_poll_answer');
			
			$this->db->where('id',$vote->id);
			$this->db->update('live_poll_vote', array(
				'live_poll_answer_id' => $ans_id
			));
			
		}else{
			// the user have not voted
			$this->db->set('num_votes','num_votes+1',FALSE);
			$this->db->where('id',$ans_id);
			$this->db->update('live_poll_answer');
			
			$this->db->insert('live_poll_vote',array(
				'live_poll_id' => $poll_id,
				'live_poll_answer_id' => $ans_id,
				'user_id' => $user_id
			));
			
		}
		$this->db->trans_complete();
		
		if($this->db->trans_status()==FALSE) return FALSE;
		return TRUE;
	}
	
	public function vote3($user_id, $poll_id,$ans_id){
		$this->db->trans_start();
		$this->db->where('user_id', $user_id);
		$this->db->where('live_poll_id',$poll_id);
		$query = $this->db->get('live_poll_vote');
		if($query->num_rows()>0){
			// the user have already voted
			$vote = $query->row();
			
			$this->db->set('num_votes','num_votes-1',FALSE);
			$this->db->where('id',$vote->live_poll_answer_id);
			$this->db->update('live_poll_answer');
			
			$this->db->set('num_votes','num_votes+1',FALSE);
			$this->db->where('id',$ans_id);
			$this->db->update('live_poll_answer');
			
			$this->db->where('id',$vote->id);
			$this->db->update('live_poll_vote', array(
				'live_poll_answer_id' => $ans_id
			));
			
		}else{
			// the user have not voted
			$this->db->set('num_votes','num_votes+1',FALSE);
			$this->db->where('id',$ans_id);
			$this->db->update('live_poll_answer');
			
			$this->db->insert('live_poll_vote',array(
				'live_poll_id' => $poll_id,
				'live_poll_answer_id' => $ans_id,
				'user_id' => $user_id
			));
			
		}
		$this->db->trans_complete();
		
		if($this->db->trans_status()==FALSE) return FALSE;
		return TRUE;
	}
	
	public function unvote($user_id, $poll_id){
		$this->db->trans_start();
		$this->db->where('user_id', $user_id);
		$this->db->where('live_poll_id',$poll_id);
		$query = $this->db->get('live_poll_vote');
		if($query->num_rows()>0){
			// the user have already voted
			$vote = $query->row();
			
			$this->db->set('num_votes','num_votes-1',FALSE);
			$this->db->where('id',$vote->live_poll_answer_id);
			$this->db->update('live_poll_answer');
			
			$this->db->where('id',$vote->id);
			$this->db->delete('live_poll_vote');
			
		}
		$this->db->trans_complete();
		
		if($this->db->trans_status()==FALSE) return FALSE;
		return TRUE;
	}
	
	

}