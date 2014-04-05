<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth extends CI_Controller {

	function __construct()
    {
        parent::__construct();
		$this->load->helper('digime');
	}

	public function access_token(){
		$username = $this->input->post('username');
		if(!$username){
			error_json(ERRORCODE_MISSING_PARAMETER,'missing parameter username');
			return;
		}
		$password = $this->input->post('password');
		if(!$username){
			error_json(ERRORCODE_MISSING_PARAMETER,'missing parameter password');
			return;
		}
		$client_key = $this->input->post('client_key');
		if(!$username){
			error_json(ERRORCODE_MISSING_PARAMETER,'missing parameter client_key');
			return;
		}
		$client_secret = $this->input->post('client_secret');
		if(!$username){
			error_json(ERRORCODE_MISSING_PARAMETER,'missing parameter client_secret');
			return;
		}
		
		$this->load->model('User_model');
		$this->load->model('Auth_model');
		
		$user = $this->User_model->authUser($username,$password);
		if(!$user){
			error_json(ERRORCODE_INVALID_USERNAME_OR_PASSWORD,'invalid username or password');
			return;
		}
		
		$client = $this->Auth_model->verifyClient($client_key, $client_secret);
		if(!$client){
			error_json(ERRORCODE_INVALID_CLIENT_KEY_OR_SECRET,'invalid client key or secret');
			return;
		}
		
		$access_token = $this->Auth_model->generateAccessToken($client->id, $user['id']);
		if(!$access_token){
			error_json(ERRORCODE_CANNOT_GENERATE_ACCESS_TOKEN,'cannot generate access token');
			return;
		}
		
		$result_array = array();
		$result_array['access_token']=$access_token;
		
		echo json_encode($result_array);
	}
	
	public function login(){
		$username = $this->input->post('username');
		if(!$username){
			error_json(ERRORCODE_MISSING_PARAMETER,'missing parameter username');
			return;
		}
		$password = $this->input->post('password');
		if(!$username){
			error_json(ERRORCODE_MISSING_PARAMETER,'missing parameter password');
			return;
		}
		$client_key = $this->input->post('client_key');
		if(!$username){
			error_json(ERRORCODE_MISSING_PARAMETER,'missing parameter client_key');
			return;
		}
		$client_secret = $this->input->post('client_secret');
		if(!$username){
			error_json(ERRORCODE_MISSING_PARAMETER,'missing parameter client_secret');
			return;
		}
		
		$this->load->model('User_model');
		$this->load->model('Auth_model');
		
		$user = $this->User_model->authUser($username,$password);
		if(!$user){
			error_json(ERRORCODE_INVALID_USERNAME_OR_PASSWORD,'invalid username or password');
			return;
		}
		
		$client = $this->Auth_model->verifyClient($client_key, $client_secret);
		if(!$client){
			error_json(ERRORCODE_INVALID_CLIENT_KEY_OR_SECRET,'invalid client key or secret');
			return;
		}
		
		$access_token = $this->Auth_model->generateAccessToken($client->id, $user['id']);
		if(!$access_token){
			error_json(ERRORCODE_CANNOT_GENERATE_ACCESS_TOKEN,'cannot generate access token');
			return;
		}
		
		$result_array = array();
		$result_array['access_token']=$access_token;
		$result_array['user']=$user;
		echo json_encode($result_array);
	}
	
	public function test_api_call(){
		$access_token = $this->input->get('access_token');
		if(!$access_token){
			error_json(ERRORCODE_MISSING_ACCESS_TOKEN, 'missing parameter access_token');
			return;
		}
		$this->load->model('Auth_model');
		$oauth_session = $this->Auth_model->verifyAccessToken($access_token);
		if(!$oauth_session){
			error_json(ERRORCODE_INVALID_ACCESS_TOKEN, 'invalid access_token');
			return;
		}
		echo $oauth_session->user_id;
	}
	
	public function exchange_token(){
		$old_access_token = $this->input->get('access_token');
		if(!$old_access_token){
			error_json(ERRORCODE_MISSING_ACCESS_TOKEN, 'missing parameter access_token');
			return;
		}	
		$this->load->model('Auth_model');
		$new_access_token = $this->Auth_model->exchangeAccessToken($old_access_token);
		if($new_access_token){
			$result_array = array();
			$result_array['access_token'] = $new_access_token;
			echo json_encode($result_array);
		}else{
			error_json(ERRORCODE_INVALID_ACCESS_TOKEN, 'invalid access_token');
			return;
		}
	}
	
	public function register(){
		$this->load->model('User_model');
		$data = array();
		
		// validate email address
		$email_address = $this->input->post('email_address');
		if(!$email_address){
			error_json(ERRORCODE_MISSING_PARAMETER,'missing required fields email_address');
			return;
		}
		$data['email_address'] = $email_address;
		
		//validate username
		$auto_username = TRUE;
		$username = $this->input->post('username');
		if($username){
			if(substr($username,0,3)=='usr'){
				error_json(ERRORCODE_USERNAME_ALREADY_EXISTS, 'username already exists');
				return;
			}
			$this->db->where('username',$username);
			$query = $this->db->get('user');
			if($query->num_rows()>0){
				error_json(ERRORCODE_USERNAME_ALREADY_EXISTS, 'username already exists');
				return;
			}
			$auto_username=FALSE;
			$data['username'] = $username;
		}
		
		// validate password
		$auto_password = FALSE;
		$password = $this->input->post('password');
		if(!$password){
			if($auto_password){
				$password = generate_random_string(16);
			}else{
				error_json(ERRORCODE_MISSING_PARAMETER,'missing parameter password');
				return;
			}
		}
		if(strlen($password)<MINIMUM_PASSWORD_LENGTH){
			error_json(ERRORCODE_PASSWORD_NOT_MATCH_SECURITY_REQUIREMENT,'password not match security requirement. minimum length is'.MINIMUM_PASSWORD_LENGTH);
			return;
		}
		$data['password'] = $password;
		
		// optional fields
		$optional_fields = $this->User_model->getOptionalFields();
		foreach($optional_fields as $field){
			
			$value = $this->input->post($field->name);
			if($value){
				$data[$field->name] = $value;
			}
		}
	
		// look for duplicate email address
		$this->db->where('email_address',$email_address);
		$query = $this->db->get('user');
		if($query->num_rows()>0){
			error_json(ERRORCODE_EMAIL_ADDRESS_ALREADY_EXISTS,'email address already exists');
			return;
		}
		
		// insert data
		$this->db->insert('user', $data);
		$insert_id = $this->db->insert_id();
		
		// update data for autousername (generated from id)
		if($auto_username){
			$update_data = array();
			$username = 'usr' . $insert_id;
			$update_data['username'] = $username;
			$this->db->where('id', $insert_id);
			$this->db->update('user', $update_data);
		}
		
		// try to create jabber id in ejabberd
		$this->config->load('ejabberd');
		$this->load->library('ejabberd_xmlrpc',array(
			'url'=>$this->config->item('ejabberd_xmlrpc_url')
		));
		
		$jabber_id = $username.'@'.$this->config->item('ejabberd_host');
			
		$result = $this->ejabberd_xmlrpc->register_user($username,$this->config->item('ejabberd_host'),$password);
		if(!isset($result['res']) || $result['res']!=0){
			// must revert the database
			$this->db->delete('user',array('id'=>$insert_id));
			error_json(ERRORCODE_EMAIL_ADDRESS_ALREADY_EXISTS,'cannot create jabber account');
			return;
		}else{
			$update_data = array();
			$update_data['jabber_id']=$jabber_id;
			$this->db->where('id', $insert_id);
			$this->db->update('user', $update_data);
		}
		
		$result_array['id'] = $insert_id;
		$result_array['username'] = $username;
		$result_array['password'] = $password;
		$result_array['email_address'] = $email_address;
		$result_array['jabber_id'] = $jabber_id;
		echo json_encode($result_array);
	}
	
	public function op(){
		$this->load->model('User_model');
		$fields = $this->User_model->getOptionalFields();
		print_r($fields);
	}
	
}