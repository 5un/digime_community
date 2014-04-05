<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users extends CI_Controller {
	
	public function index(){
		$query = $this->db->get('user');
		$digime_registered_users = $query->result();
		
		$this->load->library('ejabberd_xmlrpc',array('url'=>$this->config->item('ejabberd_xmlrpc_url')));
		
		
		
		$response = $this->ejabberd_xmlrpc->registered_users($this->config->item('ejabberd_host'));
		$ejabberd_registered_users = $response['users'];
		$ejabberd_invalid_users = $ejabberd_registered_users; 
		
		foreach($digime_registered_users as $user){
			$key = array_search(array('username'=>$user->username), $ejabberd_registered_users);
			if($key===FALSE){
				$user->in_ejabberd = false;
			}else{
				unset($ejabberd_invalid_users[$key]);
				$user->in_ejabberd = true;
			}
		}
		
		$view_data['digime_registered_users'] = $digime_registered_users;
		$view_data['ejabberd_invalid_users'] = $ejabberd_invalid_users;
		
		$this->load->view('admin/users', $view_data);
		
		//echo '<h3>registered users in ejabberd that is not in digimedb</h3>';
		
		//foreach($ejabberd_invalid_users as $user){
		//	echo '<span style="color:red">'.$user['username'].'</span>';
		//	echo '<br/>';
		//}
		
		
	}
	
	public function deleteuser(){
		
		$username = $this->input->post('username');
		$this->db->delete('user', array('username' => $username)); 
		
		$this->load->library('ejabberd_xmlrpc',array('url'=>$this->config->item('ejabberd_xmlrpc_url')));

		$response = $this->ejabberd_xmlrpc->unregister_user($username, $this->config->item('ejabberd_host'));
		
		redirect('/admin/users', 'refresh');
	}
	
	public function deleteejabberduser(){
		$username = $this->input->post('username');
		$this->load->library('ejabberd_xmlrpc',array('url'=>$this->config->item('ejabberd_xmlrpc_url')));

		$response = $this->ejabberd_xmlrpc->unregister_user($username, $this->config->item('ejabberd_host'));
		
		redirect('/admin/users', 'refresh');
	}
	
}