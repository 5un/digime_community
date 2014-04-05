<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Resource_model extends CI_Model {
	
    function __construct()
    {
        parent::__construct();
    }
		
	public function addResource($filename){
		$this->db->insert('resource',array('filename' => $filename));
		return $this->db->insert_id();
	}
	
	public function getResource($res_id){
		$this->db->where('id', $res_id);
		$query = $this->db->get('resource');
		return $query->row();
	}
	
	public function deleteResource($res_id){
		$this->db->delete('resource',array('id'=>$res_id));
	}
	
}