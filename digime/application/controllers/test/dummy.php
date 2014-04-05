<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dummy extends CI_Controller {

	public function schedule(){
		$num_generate = 200;
		$num_venues = 2;
		$rand_year = 2013;
		for($j=0;$j<$num_venues;$j++){
			for($i=0;$i<$num_generate;$i++){
				$event_title = 'Event Title ' . $i;
				$event_description = 'Description for Event' . $i;
				$rand_start_hrs = rand(8,22);
				$rand_start_day = rand(1,29);
				$rand_start_month = rand(1,12);
				$rand_start_timestamp = mktime( $rand_start_hrs ,0,0,
							$rand_start_day, $rand_start_month, $rand_year);
				$rand_start_date = date('Y-m-d H:i:s', $rand_start_timestamp);				
				
				$rand_duration = rand(1,5) * 3600;
				$rand_end_date = date('Y-m-d H:i:s', $rand_start_timestamp + $rand_duration);
				
				$insert_data = array(
					'title'=>$event_title,
					'description'=>$event_description,
					'picture'=>1,
					'start_at'=>$rand_start_date,
					'end_at'=>$rand_end_date,
					'venue_id'=>$j,
					'tags'=>'',
					'is_published'=>1,
					'live_session_id'=>0
				);
				
				$this->db->insert('schedule', $insert_data);
						
				print_r($insert_data);
				echo '<br />';
			}
		}
		
	}

}