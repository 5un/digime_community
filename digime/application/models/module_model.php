<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Module_model extends CI_Model {
	
    function __construct()
    {
        parent::__construct();
    }
	
	public function getAllModules(){
		$query = $this->db->get('module');
		return $query->result();
	}
	
	public function updateModuleEnabled($module_id, $enabled){
		$this->db->where('id',$module_id);
		$this->db->update('module',array('enabled',$enabled));
	}
	
}