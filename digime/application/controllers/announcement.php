<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Announcement extends CI_Controller {

	function __construct()
    {
        parent::__construct();
		$this->load->helper('digime');
	}
	
	public function index(){
		$result_array = array();
		
		$from_id = $this->input->get('older_than_id');
		$size = $this->input->get('size');
		
		if(!$size) $size = 20;
		if($from_id) $this->db->where('id <',$from_id);
		$this->db->order_by('id','desc');
		$this->db->limit($size);
		$query = $this->db->get('announcement');
		$announcements = $query->result_array();

		$result_array['data'] = $announcements;
		echo json_encode($result_array);
		
	}

}