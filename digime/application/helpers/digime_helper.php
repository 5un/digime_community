<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function error_json($error_code, $error_message){
	$result_array = array();
	$error_array = array();
	$error_array['code']=$error_code;
	$error_array['message']=$error_message;
	$result_array['error'] = $error_array;
	echo json_encode($result_array);
}

function generate_random_string($length){
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$string = '';    
	for ($p = 0; $p < $length; $p++) {
		$string .= $characters[mt_rand(0, strlen($characters)-1)];
	}
	return $string;
}