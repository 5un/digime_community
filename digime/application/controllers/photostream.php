<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Photostream extends CI_Controller {
	
	public function index(){
		$older_than_id = $this->input->get('older_than_id');
		$size = $this->input->get('size');
		if(!$size) $size = 10;
		
		$this->db->where('is_published',1);
		if($older_than_id) $this->db->where('id <',$older_than_id);
		$this->db->order_by('id', 'DESC');
		$this->db->limit($size);
		
		$query = $this->db->get('photostream');
		$result_array = array();
		$result_array['data'] = $query->result_array();
		
		echo json_encode($result_array);
	}
	
}