<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Auth_model extends CI_Model {
	
    function __construct()
    {
        parent::__construct();
    }
	
	public function verifyClient($client_key, $client_secret){
		$this->db->where('client_key', $client_key);
		$query = $this->db->get('oauth_client');
		if($query->num_rows()>0){
			$oauth_client = $query->row();
			if($oauth_client->client_secret==$client_secret){
				return $oauth_client;
			}else return FALSE;
		}else return FALSE;
	}
	
	public function createClient($client_name){
		
	}
	
	public function generateAccessToken($client_id,$user_id){
		$access_token = sha1(time().uniqid());
		$success = $this->db->insert('oauth_session',array(
			'client_id' => $client_id,
			'user_id' => $user_id,
			'access_token' => $access_token
		));
		if($success) return $access_token;
		else return FALSE;
	}
	
	public function exchangeAccessToken($access_token){
		$this->db->where('access_token', $access_token);
		$query = $this->db->get('oauth_session');
		if($query->num_rows()>0){
			$oauth_session = $query->row();
			$new_access_token = sha1(time().uniqid());
			$this->db->where('id', $oauth_session->id);
			$this->db->update('oauth_session', array(
					'access_token'=>$new_access_token
				));
			return $new_access_token;
		}else return FALSE;
	}
	
	public function verifyAccessToken($access_token){
		$this->db->where('access_token',$access_token);
		$query = $this->db->get('oauth_session');
		if($query->num_rows()>0){
			return $query->row();
		}else return FALSE;
	}
	
}