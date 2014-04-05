<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class DigimeFields {

  public function create_field_short_id_ai($fieldname){
    $field =  $fieldname . " INT(11) UNSIGNED NOT NULL ".
      "AUTO_INCREMENT";
    return $field;
  }

  public function create_field_short_id($fieldname){
    $field =  $fieldname . " INT(11) UNSIGNED NOT NULL";
    return $field;
  }

  public function create_field_counter($fieldname){
    $field = $fieldname . " INT(11) UNSIGNED NOT NULL " .
      "DEFAULT 0";
    return $field;
  }

  public function create_field_short_id_pk($fieldname){
    $field = $fieldname . " INT(11) UNSIGNED NOT NULL ".
      "DEFAULT NONE";
    return $field;
  }

  public function crate_field_long_id($fieldname){
    
  }

  public function create_field_short_title($fieldname){
    $field = $fieldname . " VARCHAR(255) ".
      "COLLATE utf8_unicode_ci ".
      "NOT NULL";
      return $field;
  }

  public function create_field_varchar255($fieldname){
    $field = $fieldname . " VARCHAR(255) " . 
      "COLLATE utf8_unicode_ci ".
      "NOT NULL";
    return $field;
  }

  public function create_field_body($fieldname){
    $field = $fieldname . " TEXT COLLATE utf8_unicode_ci ".
      "NOT NULL";
    return $field;
  }

  public function create_field_timestamp_created_at($fieldname){
    $field = $fieldname . " TIMESTAMP NOT NULL " .
      "DEFAULT CURRENT_TIMESTAMP";
    return $field;
  }

  public function create_field_timestamp_updated_at($fieldname){
    $field = $fieldname . " TIMESTAMP NOT NULL " .
      "";
    return $field;
  }

  public function create_field_timestamp_empty($fieldname){
    $field = $fieldname . " TIMESTAMP NOT NULL ".
      "DEFAULT '0000-00-00 00:00:00'";
    return $field;
  }

  public function create_field_flag($fieldname, $default){
    $field = $fieldname . " TINYINT(1) UNSIGNED " .
      "NOT NULL DEFAULT " . $default;
    return $field;
  }

  public function create_field_type($fieldname){
    $field = $fieldname . " TINYINT(4) UNSIGNED ".
      "NOT NULL DEFAULT 0";
    return $field;
  }
  
}
?>