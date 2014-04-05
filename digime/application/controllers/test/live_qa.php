<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Live_qa extends CI_Controller {

	public function run_all(){
		$this->load->view('admin/test_live_qa');
	}
	
	public function generate(){
		$this->load->model('LiveQA_model');
		$num_gen = 100;
		for($i=0;$i<$num_gen;$i++){
			$this->LiveQA_model->addQuestion(3,'What is Question number '.$i,1);
		}
		echo 'generate complete';
	}

}