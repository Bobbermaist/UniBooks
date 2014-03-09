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

/*
|--------------------------------------------------------------------------
| Paths
|--------------------------------------------------------------------------
|
| Used to include third party software
|
*/

define('THIRD_PARTY', FCPATH . 'application/third_party/');
define('PHPASS_PATH', THIRD_PARTY . 'phpass/');
define('UTF8_PATH', THIRD_PARTY . 'utf8/');
define('GOOGLE_API_PATH', THIRD_PARTY . 'google_api/');
define('GOOGLE_CACHE', FCPATH . APPPATH . 'cache/Google_Client/');

define('VIEWS_PATH', APPPATH . 'views/');
define('ASSETS_PATH', VIEWS_PATH . 'assets/');
define('IMG_PATH', ASSETS_PATH . 'img/');
define('CSS_PATH', ASSETS_PATH . 'css/');

/*
|--------------------------------------------------------------------------
| User
|--------------------------------------------------------------------------
|
| Users constants
| Books max results and user rights are defined here
|
*/

define('MAX_RESULTS', 10);
define('UNCONFIRMED_ACCOUNT', 0);
define('USER_RIGHTS', 1);
define('ADMIN_RIGHTS', 2);

/* End of file constants.php */
/* Location: ./application/config/constants.php */