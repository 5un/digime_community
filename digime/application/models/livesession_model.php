<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class LiveSession_model extends CI_Model {
	
	var $live_session_table = 'live_session';
	var $live_session_attendance_table = 'live_session_attendance';
	
	public function getAllSessionsCount(){
		$query = $this->db->get($this->live_session_table);
		return $query->num_rows();
	}
	
	public function getAllSessionsPaged($offset,$page_size){
		$this->db->limit($page_size,$offset);
		$query = $this->db->get($this->live_session_table);
		return $query->result_array();
	}
	
	public function getSessionByQueryCount($query_string){
		$this->db->like('title',$query_string);
		$this->db->or_like('description', $query_string);
		$this->db->or_like('tags',$query_string);
		$query = $this->db->get($this->live_session_table);
		return $query->num_rows();
	}
	
	public function getSessionByQueryPaged($query_string, $offset, $page_size){
		$this->db->like('title',$query_string);
		$this->db->or_like('description', $query_string);
		$this->db->or_like('tags',$query_string);
		$this->db->limit($page_size, $offset);
		$query = $this->db->get($this->live_session_table);
		return $query->result_array();
	}
	
	public function getSessionByID($live_session_id){
		$this->db->where('id', $live_session_id);
		$query = $this->db->get('live_session');
		return $query->row_array();
	}
	
	public function getAttendance($user_id,$live_session_id){
		$this->db->where('user_id',$user_id);
		$this->db->where('live_session_id',$live_session_id);
		$query = $this->db->get($this->live_session_attendance_table);
		if($query->num_rows()>0) return TRUE;
		else return FALSE;
	}
	
	public function getAttendanceList($user_id){
		$this->db->select('live_session_id');
		$this->db->where('user_id',$user_id);
		$query = $this->db->get($this->live_session_attendance_table);
		return $query->result_array();
	}
	
	public function attendSession($user_id, $live_session_id){
		$this->db->trans_start();
		$this->db->where('user_id',$user_id);
		$this->db->where('live_session_id',$live_session_id);
		$query = $this->db->get($this->live_session_attendance_table);
		if($query->num_rows()<=0){
			$this->db->where('id',$live_session_id);
			$query = $this->db->get($this->live_session_table);
			if($query->num_rows()<0) return FALSE; // there is no such session
			$this->db->where('id',$live_session_id);
			$this->db->set('num_attendee','num_attendee+1',FALSE);
			$this->db->update($this->live_session_table);
			
			$this->db->insert($this->live_session_attendance_table,
				array(
					'user_id'=>$user_id,
					'live_session_id'=>$live_session_id
				));
		}
		$this->db->trans_complete();
		if($this->db->trans_status()==FALSE) return FALSE;
		return TRUE;
		
	}
	
	public function unattendSession($user_id, $live_session_id){
		$this->db->trans_start();
		$this->db->where('user_id',$user_id);
		$this->db->where('live_session_id',$live_session_id);
		$query = $this->db->get($this->live_session_attendance_table);
		if($query->num_rows()>0){
			$this->db->where('id',$live_session_id);
			$query = $this->db->get($this->live_session_table);
			if($query->num_rows()<0) return FALSE; // there is no such session
			$this->db->where('id',$live_session_id);
			$this->db->set('num_attendee','num_attendee-1',FALSE);
			$this->db->update($this->live_session_table);
		
			$this->db->where('user_id',$user_id);
			$this->db->where('live_session_id',$live_session_id);
			$this->db->delete($this->live_session_attendance_table);
		}
		$this->db->trans_complete();
		if($this->db->trans_status()==FALSE) return FALSE;
		return TRUE;
	}
	
	public function clearAllData(){
		$this->db->trans_start();
		$this->db->empty_table('live_session');
		$this->db->empty_table('live_session_attendance');
		$this->db->trans_complete();
		if($this->db->trans_status()==FALSE) return FALSE;
		return TRUE;
	}
	
	public function clearAllAttendance(){
		$this->db->trans_start();
		$this->db->empty_table('live_session_attendance');
		$this->db->update('live_session',array('num_attendee'=>0));
		$this->db->trans_complete();
		if($this->db->trans_status()==FALSE) return FALSE;
		return TRUE;
	}
	
	public function clearAttendanceOfSession($session_id){
		$this->db->trans_start();
		$this->db->where('live_session_id', $session_id);
		$this->db->delete('live_session_attendance');
		$this->db->where('id',$session_id);
		$this->db->update('live_session', array('num_attendee'=>0));
		$this->db->trans_complete();
		if($this->db->trans_status()==FALSE) return FALSE;
		return TRUE;
	}
	
	public function insertSession($title,$description){
		$success = $this->db->insert($this->live_session_table, array(
			'title'=>$title,
			'description'=>$description
		));
		$insert_id = $this->db->insert_id();
		if($success) return array('result'=>TRUE,'id'=>$insert_id );
		else return array('result'=>FALSE, 'id'=>0);
	}
	
	public function getCurrentSlide(){
		
	}
	
	
	
}