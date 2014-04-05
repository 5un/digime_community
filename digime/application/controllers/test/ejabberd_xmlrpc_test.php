<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Ejabberd_xmlrpc_test extends CI_Controller {
	
	public function run_all(){
		$this->load->library('ejabberd_xmlrpc',array('url'=>'http://127.0.0.1:4560'));
		echo '<br>ejabberd_xmlrpc_registered_user :';
		print_r($this->ejabberd_xmlrpc->registered_users('jabber.digime.com'));
		echo '<br>ejabberd_xmlrpc_register_user : ';
		print_r($this->ejabberd_xmlrpc->register_user('sun2','jabber.digime.com','sun2'));
		echo '<br>ejabberd_xmlrpc_unregister_user : ';
		print_r($this->ejabberd_xmlrpc->unregister_user('sun2', 'jabber.digime.com'));
		echo '<br>';
		print_r($this->ejabberd_xmlrpc->register_user('_testUser1','jabber.digime.com','_testUser1'));
		echo '<br>';
		print_r($this->ejabberd_xmlrpc->register_user('_testUser2','jabber.digime.com','_testUser2'));
		echo '<br>';
		print_r($this->ejabberd_xmlrpc->register_user('_testUser3','jabber.digime.com','_testUser3'));
		echo '<br>ejabberd_xmlrpc_purge_users : ';
		print_r($this->ejabberd_xmlrpc->registered_users('jabber.digime.com'));
		echo '<br>';
		print_r($this->ejabberd_xmlrpc->purge_users('jabber.digime.com'));
		echo '<br>';
		print_r($this->ejabberd_xmlrpc->registered_users('jabber.digime.com'));
	}
	
	public function send(){
		$this->load->library('ejabberd_xmlrpc', array('url'=>'http://127.0.0.1:4560'));
		print_r($this->ejabberd_xmlrpc->admin_send_announcement('user1@jabber.digime.com','heyhey'));
	}
	
	public function send_multiple(){
		$this->load->library('ejabberd_xmlrpc', array('url'=>'http://127.0.0.1:4560'));
		$users = array();
		$i = 0;
		for($i=0;$i<1000;$i++){
			$users[$i] = 'user'.$i.'@jabber.digime.com';
		}
		echo 'array_fin';
		print_r($this->ejabberd_xmlrpc->admin_send_announcement_users($users,'heyhey'));
	}
	
	public function test_echo(){
		$query = $this->db->get('user');
		$users = $query->result();
		$users_jabber = array();
		
		$ejabberd_host = $this->config->item('ejabberd_host');
		$i=0;
		foreach($users as $user){
			$users_jabber[$i]=$user->username.'@'.$ejabberd_host ;
			$i++;
		}
		
		$this->load->library('ejabberd_xmlrpc',array('url'=>'http://127.0.0.1:4560'));
		print_r($this->ejabberd_xmlrpc->multhis($users_jabber));
		
	}
}