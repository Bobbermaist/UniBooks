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

define('CORE_PATH', FCPATH . 'application/core/');
define('THIRD_PARTY', FCPATH . 'application/third_party/');
define('PHPASS_PATH', THIRD_PARTY . 'phpass/');
define('UTF8_PATH', THIRD_PARTY . 'utf8/');
define('GOOGLE_API_PATH', THIRD_PARTY . 'google_api/');
define('GOOGLE_CACHE', FCPATH . APPPATH . 'cache/Google_Client/');

/*
|--------------------------------------------------------------------------
| Assets path
|--------------------------------------------------------------------------
|
| Graphic resources, css, js etc...
|
*/

define('VIEWS_PATH', APPPATH . 'views/');
define('ASSETS_PATH', VIEWS_PATH . 'assets/');
define('IMG_PATH', ASSETS_PATH . 'img/');
define('CSS_PATH', ASSETS_PATH . 'css/');

/*
|--------------------------------------------------------------------------
| User rights
|--------------------------------------------------------------------------
|
| Specify the user rights.
|
*/

define('UNCONFIRMED_ACCOUNT', 1);
define('USER_RIGHTS', 2);
define('ADMIN_RIGHTS', 3);

/*
|--------------------------------------------------------------------------
| Exceptions code values
|--------------------------------------------------------------------------
|
| The Custom exception library can be called with those values to suggest
| to the exception Class wich message should be setted. 
|
*/

define('INVALID_EXCEPTION_CODE', 0);
define('INVALID_PARAMETER', 10001);

define('ID_NON_EXISTENT', 10002);
define('USER_NAME_NON_EXISTENT', 10003);
define('EMAIL_NON_EXISTENT', 10004);
define('ISBN_NON_EXISTENT', 10005);	// refers to the database
define('GOOGLE_ID_NON_EXISTENT', 10006);

define('EXISTING_SALE', 10007);
define('EXISTING_REQUEST', 10008);

define('ISBN_NOT_FOUND', 10009); // refers to google books api
define('BOOK_NOT_FOUND', 10010);

/* End of file constants.php */
/* Location: ./application/config/constants.php */