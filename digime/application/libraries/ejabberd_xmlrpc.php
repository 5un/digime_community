<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
class Ejabberd_xmlrpc {
	
	var $service_url = '';
	var $ejabberd_vhost = 'jabber.digime.com';
	var $sender_name = 'digime';
	
	public function __construct($params)
	{
		if(array_key_exists('url',$params)){
			$this->service_url = $params['url'];
		}else{
			$this->service_url = $this->config->item('ejabberd_xmlrpc_url');
		}
	}

	private function ejabberd_xmlrpc_request($func_name, $params){
		$request = xmlrpc_encode_request($func_name, $params, (array('encoding'=>'utf-8')));
		$header = array();
		$header[] = 'User-Agent: XMLRPC::Client mod_xmlrpc';
		$header[] = 'Content-Type: text/xml';
		$header[] = 'Content-Length: '.strlen($request);
		$header[] = '\r\n';
		$ch = curl_init($this->service_url);
		curl_setopt($ch, CURLOPT_URL, $this->service_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header );
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST' );
		curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0); 
		curl_setopt($ch, CURLOPT_TIMEOUT, 400);
		$result = curl_exec($ch);
		curl_close($ch); 
		
		if($result){
			$response = xmlrpc_decode($result);
			if (is_array($response) && xmlrpc_is_fault($response)) {
				//trigger_error("xmlrpc: $response[faultString] ($response[faultCode])");
				echo 'xmlrpc is fault '.$response['faultString'].'('.$response['faultCode'].')';
			} else {
				return $response;
			}
		}else FALSE;
	}
	

	// ejabberd_xmlrpc
	public function echothis($a){
		$response = $this->ejabberd_xmlrpc_request('echothis',array('a'=>$a, 'c'=>'b'));
		return $response;
	}
	
	// ejabberd_xmlrpc
	public function multhis($users){
		$response = $this->ejabberd_xmlrpc_request('digime_send_announcement',
		array('from'=>'admin@jabber.digime.com','users'=>$users,'title'=>'Title','body'=>'testbody'));
		return $response;
	}
	
	// mod_admin
	public function status(){
		$response = $this->ejabberd_xmlrpc_request('status',array());
		return $response;
	}
	
	// mod_admin
	public function registered_users($host){
		$response = $this->ejabberd_xmlrpc_request('registered_users', array('host'=>$host));
		return $response;
	}
	
	// mod_admin
	public function register_user($user, $host, $password){
		$response = $this->ejabberd_xmlrpc_request('register', array(
			'user'=>$user,
			'host'=>$host,
			'password'=>$password
		));
		return $response;
	}
	
	// mod_admin
	public function unregister_user($user, $host){
		$response = $this->ejabberd_xmlrpc_request('unregister', array(
			'user' => $user,
			'host' => $host
		));
		return $response;
	}
	
	// mod_admin
	public function purge_users($host){
		$response = $this->registered_users($host);
		if($response){
			$users = $response['users'];
			foreach($users as $user){
				if($user['username']!='admin'){
					$this->unregister_user($user['username'], $host);
				}
			}
		}else return FALSE;
	}
	
	/*
	// using mod_admin_extra_digime
	public function send_announcement($user,$body){
		$response = $this->ejabberd_xmlrpc_request('send_message_announcement', array(
			'from'=>$this->ejabberd_vhost,
			'to'=>$user,
			'body'=>$body
		));
		return $response;
	}
	// using mod_admin_extra_digime
	public function send_announcement_users($users,$body){
		$success_all = true;
		foreach($users as $user){
			$response = $this->send_announcement($user, $body);
			if(!isset($response['res'])||$response['res']!=0){
				$success_all = false;
			}
		}
		return $success_all;
	}
	// using mod_admin_extra_digime
	public function send_announcement_all($body){
		$response = $this->ejabberd_xmlrpc_request('send_message_announcement', array(
			'from'=> $this->ejabberd_vhost,
			'to'=> $this->ejabberd_vhost.'announce/online',
			'body'=>$body
		));
		return $response;
	}
	*/
	
	// using mod_admin_extra_digime
	public function send_notification($user, $notification_id, $object, $action, $object_id, $message){
		$response = $this->ejabberd_xmlrpc_request('send_message_notification', array(
			'from'=> $this->sender_name,
			'to'=>$user,
			'id'=>$notification_id,
			'object'=>$object,
			'action'=>$action,
			'object_id'=>$object_id,
			'message'=>$message
		));
		return $response;
	}
	// using mod_admin_extra_digime
	public function send_liveslide($user, $liveslide_id, $payload){
		$response = $this->ejabberd_xmlrpc_request('send_message_liveslide', array(
			'from' => $this->sender_name,
			'to' => $user,
			'id' => $liveslide_id,
			'payload' => $payload
		));
		return $response;
	}
	// using mod_admin_extra_digime
	public function send_livepoll($user, $livepoll_id, $payload){
		$response = $this->ejabberd_xmlrpc_request('send_message_livepoll', array(
			'from' => $this->sender_name,
			'to' => $user,
			'id' => $livepoll_id,
			'payload' => $payload
		));
		return $response;
	}
	
	// using mod_digime
	public function send_announcement_users($users,$id,$title,$body){
		$admin_user = 'admin@jabber.digime.com';
		$response = $this->ejabberd_xmlrpc_request('digime_send_announcement',
		array('from'=>$admin_user, 'users'=>$users, 'id'=>''.$id, 'title'=>$title, 'body'=>$body));
		return $response;
	}
	
	// using mod_digime
	public function send_liveslide_users($users, $payload){
		$response = $this->ejabberd_xmlrpc_request('digime_send_liveslide',
		array('from'=>'admin@jabber.digime.com','users'=>$users,'payload'=>$payload));
		return $response;
	}
	
	// using mod_digime
	public function send_livepoll_users($users, $payload){
		$response = $this->ejabberd_xmlrpc_request('digime_send_livepoll',
		array('from'=>'admin@jabber.digime.com','users'=>$users,'payload'=>$payload));
		return $response;
	}
	
	// using mod_digime
	public function send_notification_users($users, $payload){
		$response = $this->ejabberd_xmlrpc_request('digime_send_notification',
		array('from'=>'admin@jabber.digime.com','users'=>$users,'payload'=>$payload));
		return $response;
	}
	
}

