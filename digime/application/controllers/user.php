<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {
	
	function __construct()
    {
        parent::__construct();
		$this->load->helper('digime');
	}
	
	private function _auth(){
		$access_token = $this->input->get('access_token');
		if(!$access_token){
			$access_token = $this->input->post('access_token');
			if(!$access_token){
				error_json(ERRORCODE_MISSING_ACCESS_TOKEN, 'missing parameter access_token');
				exit;
			}
		}
		$this->load->model('Auth_model');
		$oauth_session = $this->Auth_model->verifyAccessToken($access_token);
		if(!$oauth_session){
			error_json(ERRORCODE_INVALID_ACCESS_TOKEN, 'invalid access_token');
			exit;
		}
		return $oauth_session->user_id;
	}
	
	public function pic(){
		if($this->input->server('REQUEST_METHOD')=='POST'){
			$this->picpost();
		}else if($this->input->server('REQUEST_METHOD')=='GET'){
			$this->picget();
		}
		
	}
	
	private function picpost(){
		$access_token = $this->input->post('access_token');
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
		
		$user_id=$oauth_session->user_id;
		
		//TODO make it a config
		$targetFolder = $this->config->item('digime_resource_path');
		if (!empty($_FILES)) {
			$tempFile = $_FILES['file']['tmp_name'];
			$targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;
			$fileName = $_FILES['file']['name'];
			$targetFile = rtrim($targetPath,'/') . '/' . $_FILES['file']['name'];
	
			// Validate the file type
			$fileTypes = array('jpg','jpeg','gif','png'); // File extensions
			$fileParts = pathinfo($_FILES['file']['name']);
	
			//if (in_array($fileParts['extension'],$fileTypes)) {
				
				// add entry in resources
				$this->load->model('Resource_model');
				$res_id = $this->Resource_model->addResource($fileName);
				$res_id = sprintf("%011d", $res_id);
				$targetFile = rtrim($targetPath,'/') . '/' . $res_id;
				move_uploaded_file($tempFile,$targetFile);
				
				$this->db->where('id',$user_id);
				$this->db->update('user', array('picture'=>$res_id));
				
				//echo '{"result":true, "res_id":'.$res_id.'}';
				$result_array = array();
				$result_array['result'] = true;
				$result_array['res_id'] = $res_id;
				echo json_encode($result_array);
			
			//} else {
			//	error_json(ERRORCODE_MISSING_PARAMETER,'invalid file type');
			//}
		}else{
			echo error_json(ERRORCODE_MISSING_PARAMETER,'missing parameter file');
		}
	}
	
	private function picget(){
	
	}
	
	public function editprofile(){
		$user_id = $this->_auth();
		
		$this->load->model('User_model');
		$data = array();
		
		// validate email address
		$email_address = $this->input->post('email_address');
		if(!$email_address){
			error_json(ERRORCODE_MISSING_PARAMETER,'missing required fields email_address');
			return;
		}
		$data['email_address'] = $email_address;
		
		// optional fields
		$optional_fields = $this->User_model->getOptionalFields();
		foreach($optional_fields as $field){
			$value = $this->input->post($field->name);
			if($value){
				$data[$field->name] = $value;
			}else{
				switch($field->type){
					case FIELD_TYPE_NUMBER:
						$data[$field->name] = '0.0';
						break;
					case FIELD_TYPE_STRING:
						$data[$field->name] = '';
						break;
					case FIELD_TYPE_BOOLEAN:
						$data[$field->name] = '0';
						break;
					case FIELD_TYPE_FILE:
						$data[$field->name] = '';
						break;
					case FIELD_TYPE_DATE:
						$data[$field->name] = '0000-00-00 00:00:00';
						break;
					case FIELD_TYPE_ENUM:
						$data[$field->name] = '0';
						break;
					case FIELD_TYPE_GEOLOCATION:
						$data[$field->name] = '0.0,0.0';
						break;
				}
			}
		}
		
		// look for duplicate email address
		$this->db->where('email_address',$email_address);
		$query = $this->db->get('user');
		if($query->num_rows()>0){
			$user = $query->row();
			if($user->id!=$user_id){
				error_json(ERRORCODE_EMAIL_ADDRESS_ALREADY_EXISTS,'email address already exists');
				return;
			}
		}
		
		$this->db->where('id', $user_id);
		$success = $this->db->update('user', $data);
		if($success){
			echo '{"result":1}';
		}else{
			echo '{"result":0}';
		}
	}
	
	public function editpassword(){
		$user_id = $this->_auth();
		$new_password = $this->input->post('password');
		if(!$new_password){
			error_json(ERRORCODE_MISSING_PARAMETER,'missing parameter password');
			return;
		}
		
		// validate password here
		$length = strlen($new_password);
		if($length<MINIMUM_PASSWORD_LENGTH){
			error_json(ERRORCODE_PASSWORD_NOT_MATCH_SECURITY_REQUIREMENT,'password not match security requirement. minimum length is'.MINIMUM_PASSWORD_LENGTH);
			return;
		}
		
		$this->db->where('id', $user_id);
		$success = $this->db->update('user', array('password'=>$new_password));
		if($success){
			echo '{"result":"true"}';
		}else{
			echo '{"result":"false"}';
		}
	}
	
	public function editemail(){
	
	}
	
}