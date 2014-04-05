<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Geolocation extends CI_Controller {
	
	public function run_all(){
	
	}
	
	public function generate(){
		$num_generate = 100;
		$rand_center_lat = 13.7451;
		$rand_center_long = 100.5629;
		$rand_radius = 0.1;
		
		$this->db->where('is_published',1);
		$this->db->delete('geolocation_poi');
		
		for($i=0;$i<$num_generate;$i++){
			$rand_lat = $rand_center_lat + (rand(0,100000) - 50000) / 1000000.0;
			$rand_long = $rand_center_long + (rand(0,100000) - 50000) / 1000000.0;
			$title = 'Title for POI'.$i;
			$description = 'Description for POI'.$i;
			
			$this->db->insert('geolocation_poi',array(
				'title'=>$title,
				'description'=>$description,
				'latitude'=>$rand_lat,
				'longitude'=>$rand_long,
				'category'=>1,
				'is_published'=>1
			));
		}
	}
	
	public function select(){
		
	}
	
}