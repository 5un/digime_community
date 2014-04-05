<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class User_model extends CI_Model {
	
	var $user_table = 'user';
	var $user_field_table = 'user_field';
	var $user_settings_table = 'user_settings';
	
    function __construct()
    {
        parent::__construct();
    }
	
	//UTIL
	public static function field_construct($field_id,$name,$human_name,$type){
		$arr = array(
			'field_id' => $field_id,
			'name' => $name,
			'human_name' => $human_name,
			'type' => $type
		);
		return (object) $arr;
	}
	//
	
	public function getAllUsers(){
		$query = $this->db->get('user');
		return $query->result();
	}
	
	public function getNumAllUsers(){
		$query = $this->db->get('user');
		return $query->num_rows();
	}
	
	public function getAllUsersAsArray(){
		$query = $this->db->get($this->user_table);
		return $query->result_array();
	}
	
	public function getNumUserByQuery($query_string){
		$this->db->like('username', $query_string);
		$this->db->or_like('email_address',$query_string);
		$query = $this->db->get($this->user_table);
		return $query->num_rows();
	}
	
	public function getUserByQueryPaged($query_string,$offset, $page_size){
		$this->db->like('username', $query_string);
		$this->db->or_like('email_address',$query_string);
		$this->db->limit($page_size,$offset);
		$query = $this->db->get($this->user_table);
		return $query->result_array();
	}
	
	public function getAllUserPaged($offset,$page_size){
		$this->db->limit($page_size,$offset);
		$query = $this->db->get($this->user_table);	
		return $query->result_array();
	}
	
	public function getUserByID($user_id){
		$this->db->where('id',$user_id);
		$query = $this->db->get($this->user_table);
		return $query->result();
	}
	
	public function getUserByUsername($username){
		$this->db->where('username',$username);
		$query = $this->db->get($this->user_table);
		if($query->num_rows()>0){
			$result_array = $query->result_array();
			return $result_array[0];
		}else{
			return null;
		}
	}
	
	public function registerUser($email_address){
		$result_array = array();
		$this->db->insert('user', array(
			'email_address' => $email_address
		));
		$insert_id = $this->db->insert_id();
		$username = 'usr' . $insert_id;
		$password = $this->generateRandomString(16);
		
		$this->db->where('id', $insert_id);
		$this->db->update('user', array(
			'username' => $username,
			'password' => $password
		));
		
		$result_array['id'] = $insert_id;
		$result_array['username'] = $username;
		$result_array['password'] = $password;
		return $result_array;
	}
	
	public function registerUserWithArray($data){
		if(!is_array($data)) return FALSE;
		if(!array_key_exists('email_address', $data)) return FALSE;
		$this->db->insert('user', $data);
		
		$insert_id = $this->db->insert_id();
		$username = 'usr' . $insert_id;
		$password = $this->generateRandomString(16);
		
		$this->db->where('id', $insert_id);
		$this->db->update('user', array(
			'username' => $username,
			'password' => $password
		));
		
		$result_array['id'] = $insert_id;
		$result_array['username'] = $username;
		$result_array['password'] = $password;
		return $result_array;
	}
	
	public function authUser($username,$password){
		$this->db->where('username', $username);
		$query = $this->db->get('user');
		if($query->num_rows()>0){
			$user = $query->row_array();
			if($user['password']==$password) return $user;
			else return FALSE;
		}else return FALSE;
	}
	
	public function insertUser($data){
		// check for unknown column first
		$fields = $this->getAllFields();
		foreach($data as $key => $value){
			$found = false;
			foreach($fields as $field){
				if($field->name==$key) $found = true;
			}
			if(!$found){
				// unknown field detected
				return array('result'=>FALSE,'error'=>'unknown field detected \''.$key.'\'');
			}
		}
		
		$success = $this->db->insert($this->user_table, $data);
		return array('result'=>$success, 'error'=>'');
	}
	
	public function updateUserData($user_id, $field, $data){
		if($field=='id') return;
		$this->db->where('id',$user_id);
		$success = $this->db->update($this->user_table, array($field => $data));
		return $success;
	}
	
	public function updateUserDataWithArray($user_id, $data_array){
		$fields = $this->getAllFields();
		foreach($data_array as $key => $value){
			$found = false;
			foreach($fields as $field){
				if($field->name==$key) $found = true;
			}
			if(!$found){
				// unknown field detected
				return array('result'=>FALSE,'error'=>'unknown field detected \''.$key.'\'');
			}
		}
		
		$success = $this->db->update($this->user_table, $data_array);
		return array('result'=>$success, 'error'=>'');
	}
	
	public function deleteUser($user_id){
		$this->db->delete($this->user_table, array('id'=>$user_id));
	}
	
	public function deleteUserByUsername($username){
		$success = $this->db->delete($this->user_table, array('username'=>$username));
		return $success;
	}
	
	public function getDefaultFields(){
		$default_fields = array(
			$this->field_construct(0,'id','UserID',FIELD_TYPE_NUMBER),
			$this->field_construct(1,'username','Username',FIELD_TYPE_STRING),
			$this->field_construct(2,'password','Password',FIELD_TYPE_STRING),
			$this->field_construct(3,'email_address','Email Address',FIELD_TYPE_STRING),
			$this->field_construct(4,'picture','Picture',FIELD_TYPE_FILE)
		);
		return $default_fields;
	}
	
	public function getFieldForge($type){
		switch($type){
			case FIELD_TYPE_NUMBER:
				return array(
					'type'=>'FLOAT',
					'constraint' =>11,
					'default' => 0
				);
				break;
			case FIELD_TYPE_STRING:
				return array(
					'type' => 'VARCHAR',
					'constraint' => 255,
					'default' => ''
				);
				break;
			case FIELD_TYPE_BOOLEAN:
				return array(
					'type' => 'TINYINT',
					'constraint' => 1,
				);
				break;
			case FIELD_TYPE_FILE:
				return array(
					'type' => 'INT',
					'constraint' => 11,
					'unsigned' => TRUE
				);
				break;
			case FIELD_TYPE_DATE:
				return array(
					'type' => 'DATE'
				);
				break;
			default:
		}
	}
	
	public function getOptionalFields(){
		$query = $this->db->get($this->user_field_table);
		return $query->result();
	}
	
	public function getAllFields(){
		$default_fields = $this->getDefaultFields();
		$optional_fields = $this->getOptionalFields();
		return array_merge($default_fields,$optional_fields);
	}
	
	public function insertUserField($field_name, $field_human_name, $field_type){
		// the field must not already a mandatory fields
		$default_fields = $this->getDefaultFields();
		foreach($default_fields as $default_field){
			if($field_name==$default_field->name){
				// there is already a field of that name, error and return
				throw new Exception('The field name \''.$field_name.
				'\' is already reserved for default fields');
			}
		}
		
		// the field must not already be in optional field
		$this->db->where('name',$field_name);
		$query = $this->db->get($this->user_field_table);
		if($query->num_rows>0){
			//there is already a field of that name, error and return
			throw new Exception('There is already an optional field with the name \'' .
			$field_name . '\'');
		}
		
		// alter the table
		$this->load->dbforge();
		$new_fields = array(
			$field_name => $this->getFieldForge($field_type)
		);
		$this->dbforge->add_column($this->user_table, $new_fields);
		
		// insert the field
		$insert_field = array(
			'name' => $field_name,
			'human_name' => $field_human_name,
			'type' => $field_type
		);
		$success = $this->db->insert($this->user_field_table, $insert_field);
		if($success){
			$this->db->where('name', $field_name);
			$query = $this->db->get($this->user_field_table);
			if($query->num_rows()>0){
				$inserted_row = $query->row();
				return $inserted_row->field_id;
			}else{
				return false;
			}
		}
		
	}
	
	public function insertUserFieldAfterFieldID(
		$after_field_id,
		$field_name,
		$field_human_name,
		$field_type
	){
		
		// the field must not already a mandatory fields
		$default_fields = $this->getDefaultFields();
		foreach($default_fields as $default_field){
			if($field_name==$default_field->name){
				// there is already a field of that name, error and return
				throw new Exception('The field name \''.$field_name.
				'\' is already reserved for default fields');
			}
		}
		
		// the field must not already be in optional field
		$this->db->where('name',$field_name);
		$query = $this->db->get($this->user_field_table);
		if($query->num_rows>0){
			//there is already a field of that name, error and return
			throw new Exception('There is already an optional field with the name \'' .
			$field_name . '\'');
		}
		
		// alter the table
		$this->load->dbforge();
		$new_fields = array(
			$field_name => array(
				'type' => 'INT',
				'constraint' => 5,
				'unsigned' => TRUE,
			)
		);
		$this->dbforge->add_column($this->user_table, $new_fields);
		
		// insert the field
		$insert_field = array(
			'name' => $field_name,
			'human_name' => $field_human_name,
			'type' => $field_type
		);
		$success = $this->db->insert($this->user_field_table, $insert_field);
		if($success){
			$this->db->where('name', $field_name);
			$query = $this->db->get($this->user_field_table);
			if($query->num_rows()>0){
				$inserted_row = $query->row();
				return $inserted_row->field_id;
			}else{
				return false;
			}
		}
		
	}
	
	public function deleteField($field_name){
		// drop the column in user table
		$this->load->dbforge();
		if(!$this->db->field_exists($field_name, $this->user_table))
			throw new Exception('The field named \''. $field_name . '\' doesnt exist');
		$this->dbforge->drop_column($this->user_table,$field_name);
		//delete field information in user_field table
		$this->db->where('name',$field_name);
		$this->db->delete($this->user_field_table);
	}

	/**
	 *	Check whether if the actual structure of the 'user' table goes with the 'user_fields' table
	 */
	public function checkTableIntegrity(){		
		$actual_fields = $this->db->field_data($this->user_table);
		$logical_fields = $this->getAllFields();
		foreach($logical_fields as $lkey => $logical_field){
			foreach($actual_fields as $akey => $actual_field){
				if($logical_fields[$lkey]!=null && $actual_fields[$akey]!=null){
					if($logical_field->name==$actual_field->name){
						$logical_fields[$lkey] = null;
						$actual_fields[$akey] = null;
					}
				}
			}
		}
		
		
		
	}
	
	public function getAllSettings(){
		$query = $this->db->get($this->user_settings_table);
		return $query->result();
	}
	public function insertSetting($key, $value){
		$this->db->where('key',$key);
		$query = $this->db->get($this->user_settings_table);
		if($query->num_rows()>0){
			// the key already exists
			return false;
		}
		$insert_id = $this->db->insert($this->user_settings_table,
								array(
									'key'=>$key,
									'value'=>$value
								));
		return $insert_id;
	}
	
	public function updateSetting($key,$value){
		$this->db->where('key',$key);
		$this->db->update($this->user_settings_table, array('value'=>$value));
	}
	
	public function deleteSettingWithKey($key){
		$this->db->delete($this->user_settings_table,array('key'=>$key));
	}
	
}