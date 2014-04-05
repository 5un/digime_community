<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
 
class Install extends CI_Controller {

  public function index()
  {
    $this->load->config('digime');
    $this->createdb();
    // $this->create_resource_dir();
    $this->check_extension();
    echo '<br> digime community version installation completed';
  }

  private function check_extension(){
    if(!extension_loaded('xmlrpc')){
      echo 'WARNING: xmlrpc extension is not loaded. <br>';
      return;
    }
  }

  private function createdb(){
    $this->load->dbforge();
    $this->truncate_all_tables();
    $this->load->library('DigimeFields');
    $this->create_table_announcement();
    $this->create_table_ejabberd_command_log();
    
    $this->create_table_geolocation_category();
    $this->create_table_geolocation_poi();
    $this->create_settings_table('geolocation_settings');

    $this->create_table_live_poll();
    $this->create_table_live_poll_answer();
    $this->create_settings_table('live_poll_settings');
    $this->create_table_live_poll_vote();
    $this->create_table_live_qa();
    $this->create_settings_table('live_qa_settings');
    $this->create_table_live_qa_vote();
    $this->create_table_live_session();
    $this->create_table_live_session_attendance();
    $this->create_settings_table('live_session_settings');
    $this->create_table_live_slide();
    $this->create_table_live_slide_bookmark();
    $this->create_table_module();
    $this->create_table_news();
    $this->create_table_notification();
    $this->create_settings_table('news_settings');
    $this->create_table_oauth_client();
    $this->create_table_oauth_session();
    $this->create_table_photostream();
    $this->create_table_photostream_comment();
    $this->create_table_resource();
    $this->create_table_schedule();
    $this->create_table_schedule_dates();
    $this->create_settings_table('schedule_settings');
    $this->create_fields_table('schedule_field');
    $this->create_table_user();
    $this->create_fields_table('user_field');
    $this->create_table_venue();
    $this->create_settings_table('user_settings');
    $this->create_table_video();
	$this->create_table_ext_questions();

    //add default client key and secret 
    
    $insert_data['client_key'] = $this->config->item('default_client_key');
    $insert_data['client_secret'] = $this->config->item('default_client_secret');
    $insert_data['client_name']= $this->config->item('default_client_name');;
    $this->db->insert('oauth_client', $insert_data);
    
  }

  public function create_resource_dir(){
    $res_path = $this->config->item('digime_resource_path');
    if(file_exists($res_path)){
      if(is_writable($res_path)){
	$this->delete_all_files($res_path);
      }else{
	echo 'error: no write permission of assets/res\n';
      }
    }else{
      echo 'error: directory not found assets/res\n';
    }    
  }

  private function delete_all_files($dir){
    foreach(scandir($dir) as $file) {
      if('.'===$file || '..'=== $file) continue;
      if(!is_dir("$dir/$file")) unlink("$dir/$file");
    }
  }

  private function truncate_all_tables(){
    $tables = $this->db->list_tables();
    foreach($tables as $table){
      $this->dbforge->drop_table($table);
    }
  }

  private function create_table_announcement(){
    $this->dbforge->add_field($this->digimefields->create_field_short_id_ai('id'));
    $this->dbforge->add_field($this->digimefields->create_field_short_title('title'));
    $this->dbforge->add_field($this->digimefields->create_field_body('body'));
    $this->dbforge->add_field($this->digimefields->create_field_timestamp_created_at('created_at'));

    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('announcement');
    
  }

  private function create_table_ejabberd_command_log(){
    $this->dbforge->add_field($this->digimefields->create_field_short_id_ai('id'));
    $this->dbforge->add_field("type TINYINT(4) UNSIGNED NOT NULL");
    $this->dbforge->add_field($this->digimefields->create_field_varchar255('command'));
    $this->dbforge->add_field($this->digimefields->create_field_varchar255('result'));
    $this->dbforge->add_field($this->digimefields->create_field_timestamp_created_at('created_at'));
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('ejabberd_command_log');
  }

  private function create_table_geolocation_category(){
    $this->dbforge->add_field($this->digimefields->create_field_short_id_ai('id'));
    $this->dbforge->add_field($this->digimefields->create_field_short_title('title'));
    $this->dbforge->add_field($this->digimefields->create_field_body('description'));
    $this->dbforge->add_field($this->digimefields->create_field_short_id('pic_id'));
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('geolocation_category');

  }

  private function create_table_geolocation_poi(){
    $this->dbforge->add_field($this->digimefields->create_field_short_id_ai('id'));
    $this->dbforge->add_field($this->digimefields->create_field_short_title('title'));
    $this->dbforge->add_field($this->digimefields->create_field_body('description'));
    
    $this->dbforge->add_field($this->digimefields->create_field_short_id('category'));
    $this->dbforge->add_field($this->digimefields->create_field_flag('is_published', '0'));
    $this->dbforge->add_field($this->digimefields->create_field_varchar255('tags'));
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('geolocation_poi');
    $this->create_index_on_table('geolocation_poi', 'category_index', 'category');
  }

  private function create_table_live_poll(){
    $this->dbforge->add_field($this->digimefields->create_field_short_id_ai('id'));
    $this->dbforge->add_field($this->digimefields->create_field_short_id('live_session_id'));
    $this->dbforge->add_field($this->digimefields->create_field_short_title('title'));
    $this->dbforge->add_field($this->digimefields->create_field_body('description'));
    $this->dbforge->add_field($this->digimefields->create_field_timestamp_created_at('created_at'));
    $this->dbforge->add_field("last_vote_at TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00'");
    $this->dbforge->add_field($this->digimefields->create_field_flag('is_onetime_mode','0'));
    $this->dbforge->add_field($this->digimefields->create_field_flag('is_private_mode',0));
    $this->dbforge->add_field($this->digimefields->create_field_flag('is_published',0));
    $this->dbforge->add_field($this->digimefields->create_field_flag('is_open',0));
    $this->dbforge->add_key('id', true);
    $this->dbforge->create_table('live_poll');
  }

  private function create_table_live_poll_answer(){
    $this->dbforge->add_field($this->digimefields->create_field_short_id_ai('id'));
    $this->dbforge->add_field($this->digimefields->create_field_short_id('live_poll_id'));
    $this->dbforge->add_field($this->digimefields->create_field_short_id('answer_order'));
    $this->dbforge->add_field($this->digimefields->create_field_short_title('title'));
    $this->dbforge->add_field("num_votes INT(11) UNSIGNED NOT NULL DEFAULT 0");
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->add_key('live_poll_id', TRUE);
    $this->dbforge->create_table('live_poll_answer');
    $this->create_index_on_table('live_poll_answer', 'live_poll_id_index', 'live_poll_id');
  }

  private function create_table_live_poll_vote(){
    $this->dbforge->add_field($this->digimefields->create_field_short_id_ai('id'));
    $this->dbforge->add_field($this->digimefields->create_field_short_id('live_poll_id'));
    $this->dbforge->add_field($this->digimefields->create_field_short_id('live_poll_answer_id'));
    $this->dbforge->add_field($this->digimefields->create_field_short_id('user_id'));
    $this->dbforge->add_field($this->digimefields->create_field_timestamp_created_at('vote_at'));
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('live_poll_vote');
  }

  private function create_table_live_qa(){
    $this->dbforge->add_field($this->digimefields->create_field_short_id_ai('id'));
    $this->dbforge->add_field($this->digimefields->create_field_short_id('live_session_id'));
    $this->dbforge->add_field($this->digimefields->create_field_short_title('title'));
    $this->dbforge->add_field($this->digimefields->create_field_short_id('user_id'));
    $this->dbforge->add_field($this->digimefields->create_field_counter('up_votes'));
    $this->dbforge->add_field($this->digimefields->create_field_body('answer'));
    $this->dbforge->add_field($this->digimefields->create_field_flag('is_answered',0));
    $this->dbforge->add_field($this->digimefields->create_field_flag('is_published',0));
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('live_qa');
    $this->create_index_on_table('live_qa', 'live_session_id_index', 'live_session_id, is_published');
  }

  private function create_table_live_qa_vote(){
    $this->dbforge->add_field($this->digimefields->create_field_short_id_ai('id'));
    $this->dbforge->add_field($this->digimefields->create_field_short_id('live_qa_id'));
    $this->dbforge->add_field($this->digimefields->create_field_short_id('user_id'));
    $this->dbforge->add_field($this->digimefields->create_field_timestamp_created_at('vote_at'));
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('live_qa_vote');
    $this->create_index_on_table('live_qa_vote', 'user_id_index', 'user_id');
    $this->create_index_on_table('live_qa_vote', 'live_qa_id_index', 'live_qa_id');
  }

  private function create_table_live_session(){
    $this->dbforge->add_field($this->digimefields->create_field_short_id_ai('id'));
    $this->dbforge->add_field($this->digimefields->create_field_short_title('title'));
    $this->dbforge->add_field($this->digimefields->create_field_body('description'));
    $this->dbforge->add_field($this->digimefields->create_field_varchar255('speaker'));
    $this->dbforge->add_field($this->digimefields->create_field_short_id('pic_id'));
    $this->dbforge->add_field($this->digimefields->create_field_varchar255('venue'));
    $this->dbforge->add_field($this->digimefields->create_field_varchar255('tags'));
    $this->dbforge->add_field($this->digimefields->create_field_counter('num_attendee'));
    $this->dbforge->add_field($this->digimefields->create_field_counter('max_attendee'));
    $this->dbforge->add_field($this->digimefields->create_field_short_id('schedule_id'));
    $this->dbforge->add_field($this->digimefields->create_field_flag('is_published','0'));
    $this->dbforge->add_field($this->digimefields->create_field_flag('is_live','0'));
    $this->dbforge->add_key('id',TRUE);
    $this->dbforge->create_table('live_session');
    $this->create_index_on_table('live_session','tags_index', 'tags');
    $this->create_index_on_table('live_session','schedule_id_index', 'schedule_id');
  }
  
  private function create_table_live_session_attendance(){
    $this->dbforge->add_field($this->digimefields->create_field_short_id_ai('id'));
    $this->dbforge->add_field($this->digimefields->create_field_short_id('live_session_id'));
    $this->dbforge->add_field($this->digimefields->create_field_short_id('user_id'));
    $this->dbforge->add_field($this->digimefields->create_field_timestamp_updated_at('attend_at'));
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('live_session_attendance');
    $this->create_index_on_table('live_session_attendance', 'user_id_index', 'user_id');
  }

  private function create_table_live_slide(){
    $this->dbforge->add_field($this->digimefields->create_field_short_id_ai('id'));
    $this->dbforge->add_field($this->digimefields->create_field_short_id('live_session_id'));
    $this->dbforge->add_field($this->digimefields->create_field_short_title('title'));
    $this->dbforge->add_field($this->digimefields->create_field_short_id('res_id'));
    $this->dbforge->add_field($this->digimefields->create_field_short_id('thumb_id'));
    $this->dbforge->add_field($this->digimefields->create_field_flag('is_published', '0'));
    $this->dbforge->add_field($this->digimefields->create_field_flag('is_presented', '0'));
    $this->dbforge->add_field($this->digimefields->create_field_timestamp_created_at('presented_at'));
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('live_slide');
    $this->create_index_on_table('live_slide', 'live_session_id_index', 'live_session_id');
  }
 
  private function create_table_live_slide_bookmark(){
    $this->dbforge->add_field($this->digimefields->create_field_short_id_ai('id'));
    $this->dbforge->add_field($this->digimefields->create_field_short_id('live_slide_id'));
    $this->dbforge->add_field($this->digimefields->create_field_short_id('user_id'));
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('live_slide_bookmark');
    $this->create_index_on_table('live_slide_bookmark','user_id_index', 'user_id');
  }

  private function create_table_module(){
    $this->dbforge->add_field($this->digimefields->create_field_short_id_ai('id'));
    $this->dbforge->add_field("name VARCHAR(50) NOT NULL COLLATE utf8_unicode_ci");
    $this->dbforge->add_field($this->digimefields->create_field_flag('enabled', '0'));
    $this->dbforge->add_field($this->digimefields->create_field_short_id('icon'));
    $this->dbforge->add_field($this->digimefields->create_field_body('text'));
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('module');
  }

  private function create_table_news(){
    $this->dbforge->add_field($this->digimefields->create_field_short_id_ai('id'));
    $this->dbforge->add_field($this->digimefields->create_field_short_title('title'));
    $this->dbforge->add_field($this->digimefields->create_field_body('body'));
    $this->dbforge->add_field($this->digimefields->create_field_timestamp_created_at('created_at'));
    $this->dbforge->add_field($this->digimefields->create_field_timestamp_updated_at('updated_at'));
    $this->dbforge->add_field($this->digimefields->create_field_varchar255('tags'));
    $this->dbforge->add_field($this->digimefields->create_field_short_id('pic_id'));
    $this->dbforge->add_field($this->digimefields->create_field_flag('is_published', '0'));
    $this->dbforge->add_field($this->digimefields->create_field_counter('num_views'));
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('news');
    $this->create_index_on_table('news', 'is_published_index', 'is_published');
  }

  private function create_table_notification(){
    $this->dbforge->add_field($this->digimefields->create_field_short_id_ai('id'));
    $this->dbforge->add_field($this->digimefields->create_field_short_id('user_id'));
    $this->dbforge->add_field("message VARCHAR(1023) COLLATE utf8_unicode_ci");
    $this->dbforge->add_field($this->digimefields->create_field_timestamp_created_at('created_at'));
    $this->dbforge->add_field($this->digimefields->create_field_flag('is_read', '0'));
    $this->dbforge->add_field($this->digimefields->create_field_type('type'));
    $this->dbforge->add_key(array('id','user_id'), TRUE);
    $this->dbforge->create_table('notification');
    $this->create_index_on_table('notification', 'user_id_index', 'user_id');
  }

  private function create_table_oauth_client(){
    $this->dbforge->add_field($this->digimefields->create_field_short_id_ai('id'));
    $this->dbforge->add_field("client_key VARCHAR(50) NOT NULL COLLATE utf8_unicode_ci");
    $this->dbforge->add_field("client_secret VARCHAR(50) NOT NULL COLLATE utf8_unicode_ci");
    $this->dbforge->add_field("client_name VARCHAR(50) NOT NULL COLLATE utf8_unicode_ci");
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('oauth_client');
    $this->create_index_on_table('oauth_client', 'client_key_index', 'client_key');
}

  private function create_table_oauth_session(){
    $this->dbforge->add_field($this->digimefields->create_field_short_id_ai('id'));
    $this->dbforge->add_field("access_token BINARY(40)");
    $this->dbforge->add_field($this->digimefields->create_field_short_id('user_id'));
    $this->dbforge->add_field($this->digimefields->create_field_short_id('client_id'));
    $this->dbforge->add_field($this->digimefields->create_field_timestamp_created_at('start_at'));
    $this->dbforge->add_field($this->digimefields->create_field_varchar255('scope'));
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('oauth_session');
    $this->create_index_on_table('oauth_session', 'access_token_index', 'access_token');
  }

  private function create_table_photostream(){
    $this->dbforge->add_field($this->digimefields->create_field_short_id_ai('id'));
    $this->dbforge->add_field($this->digimefields->create_field_short_id('pic_id'));
    $this->dbforge->add_field($this->digimefields->create_field_short_id('thumb_id'));
    $this->dbforge->add_field($this->digimefields->create_field_short_title('title'));
    $this->dbforge->add_field($this->digimefields->create_field_body('description'));
    $this->dbforge->add_field($this->digimefields->create_field_short_id('user_id'));
    $this->dbforge->add_field($this->digimefields->create_field_varchar255('user_display_name'));
    $this->dbforge->add_field($this->digimefields->create_field_timestamp_created_at('created_at'));
    $this->dbforge->add_field($this->digimefields->create_field_flag('is_published','0'));
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('photostream');
  }

  private function create_table_photostream_comment(){
    $this->dbforge->add_field($this->digimefields->create_field_short_id_ai('id'));
    $this->dbforge->add_field($this->digimefields->create_field_short_id('photostream_id'));
    $this->dbforge->add_field($this->digimefields->create_field_short_id('user_id'));
    $this->dbforge->add_field($this->digimefields->create_field_varchar255('user_display_name'));
    $this->dbforge->add_field($this->digimefields->create_field_body('comment'));
    $this->dbforge->add_field($this->digimefields->create_field_timestamp_created_at('created_at'));
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('photostream_comment');
    $this->create_index_on_table('photostream_comment', 'photostream_id_index', 'photostream_id');
    
  }

  private function create_table_resource(){
    $this->dbforge->add_field($this->digimefields->create_field_short_id_ai('id'));
    $this->dbforge->add_field($this->digimefields->create_field_varchar255('filename'));
    $this->dbforge->add_field($this->digimefields->create_field_timestamp_created_at('created_at'));
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('resource');
    $this->create_unique_index_on_table('resource', 'id_index', 'id');
  }

  private function create_table_schedule(){
    $this->dbforge->add_field($this->digimefields->create_field_short_id_ai('id'));
    $this->dbforge->add_field($this->digimefields->create_field_short_title('title'));
    $this->dbforge->add_field($this->digimefields->create_field_body('description'));
    $this->dbforge->add_field($this->digimefields->create_field_short_id('picture'));
    $this->dbforge->add_field($this->digimefields->create_field_timestamp_empty('start_at'));
    $this->dbforge->add_field($this->digimefields->create_field_timestamp_empty('end_at'));
    $this->dbforge->add_field($this->digimefields->create_field_short_id('venue_id'));
    $this->dbforge->add_field($this->digimefields->create_field_varchar255('tags'));
    $this->dbforge->add_field($this->digimefields->create_field_flag('is_published','0'));
    $this->dbforge->add_field($this->digimefields->create_field_short_id('live_session_id'));
    $this->dbforge->add_field($this->digimefields->create_field_body('data'));
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('schedule');
  }

  private function create_table_schedule_dates(){
    $this->dbforge->add_field($this->digimefields->create_field_short_id_ai('id'));
    $this->dbforge->add_field($this->digimefields->create_field_short_title('title'));
    $this->dbforge->add_field($this->digimefields->create_field_timestamp_created_at('date'));
    $this->dbforge->add_key('id',TRUE);
    $this->dbforge->create_table('schedule_dates');
  }

  private function create_table_user(){
    $this->dbforge->add_field($this->digimefields->create_field_short_id_ai('id'));
    $this->dbforge->add_field("username VARCHAR(50) NOT NULL COLLATE utf8_unicode_ci");
    $this->dbforge->add_field("password VARCHAR(50) NOT NULL COLLATE utf8_unicode_ci");
    $this->dbforge->add_field("email_address VARCHAR(50) NOT NULL COLLATE utf8_unicode_ci");
    $this->dbforge->add_field("gmail_account VARCHAR(50) NOT NULL COLLATE utf8_unicode_ci");
    $this->dbforge->add_field("jabber_id VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci");
    $this->dbforge->add_field("display_name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci");
    $this->dbforge->add_field($this->digimefields->create_field_short_id('picture'));
    $this->dbforge->add_field($this->digimefields->create_field_short_id('role'));
    $this->dbforge->add_field($this->digimefields->create_field_body('data'));
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('user');
    $this->create_unique_index_on_table('user', 'username_index', 'username');
    
  }

  private function create_fields_table($tablename){
    $this->dbforge->add_field($this->digimefields->create_field_short_id_ai('field_id'));
    $this->dbforge->add_field("name VARCHAR(50) NOT NULL COLLATE utf8_unicode_ci");
    $this->dbforge->add_field("human_name VARCHAR(50) NOT NULL COLLATE utf8_unicode_ci");
    $this->dbforge->add_field($this->digimefields->create_field_type('type'));
    $this->dbforge->add_field($this->digimefields->create_field_varchar255('possible_values'));
    $this->dbforge->add_field($this->digimefields->create_field_flag('required_at_register','0'));
    $this->dbforge->add_key('field_id', TRUE);
    $this->dbforge->create_table($tablename);

  }

  private function create_table_venue(){
    $this->dbforge->add_field($this->digimefields->create_field_short_id_ai('id'));
    $this->dbforge->add_field($this->digimefields->create_field_short_title('title'));
    $this->dbforge->add_field($this->digimefields->create_field_body('description'));
    $this->dbforge->add_field($this->digimefields->create_field_short_id('geolocation_poi_id'));
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('venue');
  }

  private function create_table_video(){
    $this->dbforge->add_field($this->digimefields->create_field_short_id_ai('id'));
    $this->dbforge->add_field($this->digimefields->create_field_short_title('title'));
    $this->dbforge->add_field($this->digimefields->create_field_body('description'));
    $this->dbforge->add_field($this->digimefields->create_field_varchar255('duration'));
    $this->dbforge->add_field($this->digimefields->create_field_varchar255('category'));
    $this->dbforge->add_field($this->digimefields->create_field_short_id('preview_pic'));
    $this->dbforge->add_field($this->digimefields->create_field_short_id('version'));
    $this->dbforge->add_field('md5_checksum BINARY(32) NOT NULL');
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('video');
  }

  private function create_table_ext_questions(){
	 $this->dbforge->add_field($this->digimefields->create_field_short_id_ai('id'));
	 $this->dbforge->add_field($this->digimefields->create_field_short_id('user_id'));
	 $this->dbforge->add_field($this->digimefields->create_field_body('question'));
	 $this->dbforge->add_field($this->digimefields->create_field_timestamp_created_at('created_at'));
	 $this->dbforge->add_key('id', TRUE);
	 $this->dbforge->create_table('ext_questions');
  }
  
  private function create_settings_table($tablename){
    $this->dbforge->add_field($this->digimefields->create_field_short_id_ai('id'));
    $this->dbforge->add_field($this->digimefields->create_field_varchar255('skey'));
    $this->dbforge->add_field($this->digimefields->create_field_varchar255('value'));
    $this->dbforge->add_field($this->digimefields->create_field_type('type'));
    $this->dbforge->add_field($this->digimefields->create_field_body('description'));
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table($tablename);
    $this->create_index_on_table($tablename, 'skey_index', 'skey');
  }

  private function create_unique_index_on_table($tablename, $indexname, $column){
    $this->db->query("CREATE UNIQUE INDEX " . $indexname . " ON " . $tablename . " (".$column.")");
  }

  private function create_index_on_table($tablename, $indexname, $column){
    $this->db->query("CREATE INDEX " . $indexname . " ON " . $tablename . " (" . $column . ")");
  }
  
}
