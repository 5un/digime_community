<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class News extends CI_Controller {

	function __construct()
    {
        parent::__construct();
		$this->load->helper('digime');
	}

	public function index(){
		$result_array = array();
		$news_id = $this->input->get('id');
		if($news_id){
			$this->db->where('id',$news_id);
			$this->db->where('is_published', 1);
			$query = $this->db->get('news');
			if($query->num_rows()>0){
				$news_entry = $query->row_array();
				$result_array['data']=$news_entry;
				$this->output->set_status_header('200');
				echo json_encode($result_array);
				return;
			}else{
				//$this->output->set_status_header('404');
				error_json(ERRORCODE_INVALID_OBJECT_ID,'The news is non-existent.');
				return;
			}
		}else{
			$query_string = $this->input->get('query');
			$from_id = $this->input->get('older_than_id');
			$size = $this->input->get('size');
			if(!$size) $size = 10;
			
			if($query_string==''){
				$this->db->where('is_published',1);
				if($from_id) $this->db->where('id <',$from_id);
				$this->db->limit($size);
				$this->db->order_by('id','desc');
				$query = $this->db->get('news');
				$news_entries = $query->result_array();
			}else{
				$wildcard_query_string = '%'.$this->db->escape_like_str($query_string).'%';
				if($from_id>0){
					$sql = "SELECT * FROM `news` WHERE `id` < ? AND `is_published` = 1 AND (`title` LIKE ? OR `tags` LIKE ?) ORDER BY `id` DESC LIMIT ?";
					$query = $this->db->query($sql,array(intval($from_id),$wildcard_query_string,$wildcard_query_string,intval($size)));
				}else{
					$sql = "SELECT * FROM `news` WHERE `is_published` = 1 AND (`title` LIKE ? OR `tags` LIKE ?) ORDER BY id DESC LIMIT ?";
					$query = $this->db->query($sql,array($wildcard_query_string,$wildcard_query_string,$size));
				}
				$news_entries = $query->result_array();
			}
			$this->output->set_status_header('200');
			$result_array['data'] = $news_entries;
			echo json_encode($result_array);
			return;
		}
	}

}
	