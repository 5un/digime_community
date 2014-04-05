<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

/*other constants for digime*/
define("FIELD_TYPE_NUMBER",0);
define("FIELD_TYPE_STRING",1);
define("FIELD_TYPE_BOOLEAN",2);
define("FIELD_TYPE_FILE",3);
define("FIELD_TYPE_DATE",4);
define("FIELD_TYPE_ENUM",5);
define("FIELD_TYPE_GEOLOCATION",6);

define('ERRORCODE_UNKNOWN',							999);
define('ERRORCODE_INVALID_ACCESS_TOKEN',			1000);
define('ERRORCODE_INVALID_USER_ID',					1001);
define('ERRORCODE_INVALID_LIVE_SESSION_ID',			1002);
define('ERRORCODE_INVALID_LIVE_QA_ID',				1003);
define('ERRORCODE_INVALID_LIVE_POLL_ID',			1004);
define('ERRORCODE_INVALID_LIVE_POLL_ANS_ID',		1005);
define('ERRORCODE_EMAIL_ADDRESS_ALREADY_EXISTS',	1006);
define('ERRORCODE_MISSING_PARAMETER',				1007);
define('ERRORCODE_USERNAME_ALREADY_EXISTS',			1008);
define('ERRORCODE_INVALID_USERNAME_OR_PASSWORD',	1009);
define('ERRORCODE_INVALID_CLIENT_KEY_OR_SECRET',	1010);
define('ERRORCODE_CANNOT_GENERATE_ACCESS_TOKEN',	1011);
define('ERRORCODE_MISSING_ACCESS_TOKEN',			1012);
define('ERRORCODE_INVALID_OBJECT_ID',				1013);
define('ERRORCODE_PASSWORD_NOT_MATCH_SECURITY_REQUIREMENT', 1014);

define('MINIMUM_PASSWORD_LENGTH',	6);

/* End of file constants.php */
/* Location: ./application/config/constants.php */