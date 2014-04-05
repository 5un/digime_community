<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Clipdata extends CI_Controller {

	public function index(){
		$this->db->where('id >',460);
		$this->db->delete('user');
	}
	
	public function jabberreg(){
		$username = "wertwert2";
		$password = "wertwert2";
		
		$this->config->load('ejabberd');
		$this->load->library('ejabberd_xmlrpc',array(
			'url'=>$this->config->item('ejabberd_xmlrpc_url')
		));

		$jabber_id = $username.'@'.$this->config->item('ejabberd_host');
		
		echo "host:".$this->config->item('ejabberd_host')." ".$this->config->item('ejabberd_xmlrpc_url');
		
		$result = $this->ejabberd_xmlrpc->register_user($username,$this->config->item('ejabberd_host'),$password);
		print_r($result);
		
	}

}