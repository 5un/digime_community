<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Schedule extends CI_Controller {

	function __construct()
    {
        parent::__construct();
		$this->load->helper('digime');
	}

	public function index(){
		$result_array = array();
		$schedule_id = $this->input->get('id');
		if($schedule_id){
			$this->db->select('schedule.id,schedule.title,schedule.description,schedule.picture,schedule.start_at,schedule.end_at,schedule.venue_id,venue.title as venue_title,schedule.tags,schedule.is_published');
			$this->db->from('schedule');
			$this->db->where('schedule.id', $schedule_id);
			$this->db->where('schedule.is_published', 1);
			$this->db->join('venue','venue.id = schedule.venue_id');
			$query = $this->db->get();
			if($query->num_rows()>0){
				$schedule = $query->row_array();
				$result_array['data']=$schedule;
				$this->output->set_status_header('200');
				echo json_encode($result_array);
				return;
			}else{
				//$this->output->set_status_header('404');
				error_json(ERRORCODE_INVALID_OBJECT_ID,'The schedule is non-existent.');
				return;
			}
		}else{
			$query_string = $this->input->get('query');
			$older_than_id = $this->input->get('older_than_id');
			$size = $this->input->get('size');
			if(!$size) $size = 10;
			if($query_string==''){
				$this->db->select('schedule.id,schedule.title,schedule.description,schedule.picture,schedule.start_at,schedule.end_at,schedule.venue_id,venue.title as venue_title,schedule.tags,schedule.is_published');
				$this->db->from('schedule');
				$this->db->where('schedule.is_published', 1);
				if($older_than_id > 0) $this->db->where('schedule.id <', $older_than_id);
				$this->db->join('venue','venue.id = schedule.venue_id');
				$this->db->limit($size);
				$query = $this->db->get();
				$schedules = $query->result_array();
			}else{
				$wildcarded_query_string = '%'.$this->db->escape_like_str($query_string).'%';
				if($older_than_id>0){
					$sql = "SELECT schedule.id, schedule.title, schedule.description, schedule.picture, schedule.start_at, schedule.end_at, schedule.venue_id,venue.title as venue_title,schedule.tags,schedule.is_published ".
					"FROM schedule LEFT JOIN venue ON (venue.id = schedule.venue_id) ".
					"WHERE schedule.is_published = 1 AND schedule.id < ? AND ".
					"(schedule.title LIKE ? OR schedule.tags LIKE ?) ".
					"ORDER BY schedule.id DESC LIMIT ?";
					$query = $this->db->query($sql,array(intval($older_than_id), $wildcarded_query_string, $wildcarded_query_string, intval($size)));
				}else{
					$sql = "SELECT schedule.id, schedule.title, schedule.description, schedule.picture, schedule.start_at, schedule.end_at, schedule.venue_id,venue.title as venue_title,schedule.tags,schedule.is_published ".
					"FROM schedule LEFT JOIN venue ON (venue.id = schedule.venue_id) ".
					"WHERE schedule.is_published = 1 AND ".
					"(schedule.title LIKE ? OR schedule.tags LIKE ?) ".
					"ORDER BY schedule.id DESC LIMIT ?";
					$query = $this->db->query($sql,array($wildcarded_query_string, $wildcarded_query_string, intval($size)));
				}
				$schedules = $query->result_array();
			}
			$this->output->set_status_header('200');
			$result_array['data'] = $schedules;
			echo json_encode($result_array);
			return;
		}
	}
	
	public function day(){
		
		$q_date = $this->input->get('date');
		if(!$q_date){
			error_json(ERRORCODE_MISSING_PARAMETER,'missing parameter date');
			return;
		}
		$start_of_day = $q_date . " 00:00:00";
		$end_of_day = $q_date . " 23:59:59";
		$sql = "SELECT schedule.id,schedule.title,schedule.description,schedule.picture,schedule.start_at,schedule.end_at,schedule.venue_id,venue.title as venue_title,schedule.tags,schedule.is_published ".
		"FROM schedule LEFT JOIN venue ON (venue.id = schedule.venue_id) ".
		"WHERE schedule.is_published = 1 AND ((schedule.start_at BETWEEN ? AND ?) OR (schedule.end_at BETWEEN ? AND ?) OR (? BETWEEN schedule.start_at AND schedule.end_at) OR (? BETWEEN schedule.start_at AND schedule.end_at))";
		$query = $this->db->query($sql, array($start_of_day, $end_of_day,$start_of_day,$end_of_day,$start_of_day, $end_of_day));
		$schedules = $query->result_array();
		$this->output->set_status_header('200');
		$result_array['data'] = $schedules;
		echo json_encode($result_array);
	}
	
	public function upcoming(){
		$q_timestamp = $this->input->get('timestamp');
		$size = $this->input->get('size');
		if(!$q_timestamp){
			error_json(ERRORCODE_MISSING_PARAMETER,'missing parameter timestamp');
			return;
		}
		if(!$size) $size = 20;
		$sql = "SELECT schedule.id,schedule.title,schedule.description,schedule.picture,schedule.start_at,schedule.end_at,schedule.venue_id,venue.title as venue_title,schedule.tags,schedule.is_published ".
		"FROM schedule LEFT JOIN venue ON (venue.id = schedule.venue_id) ".
		"WHERE schedule.is_published = 1 AND schedule.start_at > ? ".
		"ORDER BY schedule.start_at ASC LIMIT ?";
		$query = $this->db->query($sql, array($q_timestamp,intval($size)));
		$schedules = $query->result_array();
		$this->output->set_status_header('200');
		$result_array['data'] = $schedules;
		echo json_encode($result_array);
	}
	
	public function ongoing(){
		
	}
	
}