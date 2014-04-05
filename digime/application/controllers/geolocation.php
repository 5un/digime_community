<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Geolocation extends CI_Controller {
	
	function __construct()
    {
        parent::__construct();
		$this->load->helper('digime');
	}
	
	public function index(){
		$result_array = array();
		$poi_id = $this->input->get('id');
		if($poi_id){
			$this->db->where('id', $poi_id);
			$this->db->where('is_published', 1);
			$query = $this->db->get('geolocation_poi');
			if($query->num_rows()>0){
				$poi = $query->row_array();
				$result_array['data']=$poi;
				$this->output->set_status_header('200');
				echo json_encode($result_array);
				return;
			}else{
				//$this->output->set_status_header('404');
				error_json(ERRORCODE_INVALID_OBJECT_ID,'The point of interest is non-existent.');
			}
		}else{
			$query_string = $this->input->get('query');
			$size = $this->input->get('size');
			$user_lat = $this->input->get('lat');
			$user_long = $this->input->get('long');
			$user_radius = $this->input->get('radius');
			
			if(!$size) $size = 10;
			if($query_string){
				$wildcarded_query_string = '%'.$this->db->escape_like_str($query_string).'%';
				$sql = "SELECT * FROM geolocation_poi ".
				"WHERE is_published = 1 AND (title LIKE ? OR description LIKE ?)".
				"ORDER BY id DESC LIMIT ?";
				$query = $this->db->query($sql, array($wildcarded_query_string, $wildcarded_query_string, intval($size)));
			}else{
				$this->db->where('is_published',1);
				$this->db->limit($size);
				$query = $this->db->get('geolocation_poi');
			}

			$pois = $query->result_array();
			$result_array['data']=$pois;
			$this->output->set_status_header('200');
			echo json_encode($result_array);
			return;
		}
	}
	
	public function venue(){
		$venue_id = $this->input->get('id');
		
		$this->db->where('id', $venue_id);
		$query = $this->db->get('venue');
		
		if($query->num_rows()<=0){
			error_json(ERRORCODE_INVALID_OBJECT_ID,'The venue is non-existent.');
			return;
		}
		
		$venue = $query->row();
		$this->db->where('id', $venue->geolocation_poi_id);
		$query = $this->db->get('geolocation_poi');
		
		if($query->num_rows()<=0){
			error_json(ERRORCODE_INVALID_OBJECT_ID,'The point of interest is non-existent.');
			return;
		}
		
		$poi = $query->row_array();
		
		$result_array['data'] = $poi;
		echo json_encode($result_array);
	}
	
}