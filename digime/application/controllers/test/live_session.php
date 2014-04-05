<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Live_session extends CI_Controller {
	
	public function runall(){
		$this->load->view('admin/test_live_session');
	}
	
	public function generate(){
		$this->load->model('LiveSession_model');
		$num_generate = 100;
		for($i=0;$i<$num_generate;$i++){
			$this->LiveSession_model->insertSession('session'.$i,'Description for session ' . $i);
		}
		echo 'generate complete';
	}
}