<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Schedule_model extends CI_Model {

	var $schedule_table = 'schedule';
	var $schedule_field_table = 'schedule_table';
	
    function __construct()
    {
        parent::__construct();
    }
	
	public static function field_construct($field_id,$name,$human_name,$type){
		$arr = array(
			'field_id' => $field_id,
			'name' => $name,
			'human_name' => $human_name,
			'type' => $type
		);
		return (object) $arr;
	}
	
	public function getDefaultFields(){
		$default_fields = array(
			$this->field_construct(0,'id','UserID',FIELD_TYPE_NUMBER),
			$this->field_construct(1,'title','Title',FIELD_TYPE_STRING),
			$this->field_construct(2,'description','Description',FIELD_TYPE_STRING),
			$this->field_construct(3,'start_at','Start At',FIELD_TYPE_DATE),
			$this->field_construct(4,'end_at','End At',FIELD_TYPE_DATE),
			$this->field_construct(5,'venue_id','venue_id',FIELD_TYPE_NUMBER),
			$this->field_construct(6,'tags','Tags',FIELD_TYPE_STRING)
		);
		return $default_fields;
	}
	
	public function getOptionalFields(){
		$query = $this->db->get($this->user_field_table);
		return $query->result();
	}
	
	public function getAllFields(){
		$default_fields = $this->getDefaultFields();
		$optional_fields = $this->getOptionalFields();
		return array_merge($default_fields,$optional_fields);
	}
	
	public function getAllSchedule(){
		$query = $this->db->get($this->schedule_table);
		return $query->result_array();
	}
	
	public function getAllScheduleCount(){
		$query = $this->db->get($this->schedule_table);
		return $query->num_rows();
	}
	
	public function getAllSchedulePaged($offset, $page_size){
		$this->db->limit($page_size,$offset);
		$query = $this->db->get($this->schedule_table);
		return $query->result_array();
	}
	
	public function getScheduleInTimeRange($startTime, $endTime){
		
	}
	
	public function getScheduleAtTime($time){
		
	}
	
	public function getScheduleByVenue($venue_id){
		
	}
	
	public function getScheduleByVenuePaged(){
	
	}
	
	public function getScheduleByQuery($query_string){
		$this->db->like('title', $query_string);
		$this->db->or_like('description',$query_string);
		$this->db->or_like('tags',$query_string);
		$query = $this->db->get($this->schedule_table);
	}
	
	public function getScheduleCountByQuery($query_string){
		
	}
	
	public function getScheduleByQueryPaged($query_string,$offset,$page_size){
		$this->db->like('title', $query_string);
		$this->db->or_like('description',$query_string);
		$this->db->or_like('tags',$query_string);
		$this->db->limit($page_size,$offset);
		$query = $this->db->get($this->schedule_table);
	}
	
	public function insertSchedule($data){
		//$fields = getAllFields();
	}
	
	public function updateSchedule(){
	
	}
	
	public function deleteSchedule($schedule_id){
		$this->db->delete($this->schedule_table, array('id'=>$schedule_id));
	}
	
	public function clearAllSchedule(){
		$this->db->delete($this->schedule_table);
	}
}