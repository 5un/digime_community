<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ejabberd_xmlrpc_test extends CI_Controller {
	
	public function run_all(){
		$this->load->library('ejabberd_xmlrpc');
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
}