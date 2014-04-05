<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Live_poll extends CI_Controller {
	
	public function runall(){
		$this->load->view('admin/test_live_poll');
	}
	
	public function generate(){
		$this->load->model('LivePoll_model');
		$num_gen = 1000;
		$num_ans = 5;
		for($i=0;$i<$num_gen;$i++){
			$poll_id = $this->LivePoll_model->createPoll('testpoll'.$i);
			if($poll_id!=FALSE){
				for($j=0;$j<$num_ans;$j++){
					$this->LivePoll_model->addAnswerToPoll($poll_id,'testansPoll'.$i.'Ans'.$j);
				}
			}
		}
		echo 'generate complete';
	}
}