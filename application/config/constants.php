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

/** Absolute path to ./application/core/ */
define('CORE_PATH', FCPATH . 'application/core/');

/** Absolute path to ./application/third_party/ */
define('THIRD_PARTY', FCPATH . 'application/third_party/');

/** Absolute path to ./application/third_party/phpass/ */
define('PHPASS_PATH', THIRD_PARTY . 'phpass/');

/** Absolute path to ./application/third_party/utf8/ */
define('UTF8_PATH', THIRD_PARTY . 'utf8/');

/** Absolute path to ./application/third_party/google_api/ */
define('GOOGLE_API_PATH', THIRD_PARTY . 'google_api/');

/** 
 * Absolute path to ./application/cache/Google_Client/
 *
 * Here will be saved all cache produced by google api
 */
define('GOOGLE_CACHE', FCPATH . APPPATH . 'cache/Google_Client/');

/*
|--------------------------------------------------------------------------
| Assets path
|--------------------------------------------------------------------------
|
| Graphic resources, css, js etc...
|
*/

/** Path to ./application/views/ */
define('VIEWS_PATH', APPPATH . 'views/');

/** Path to ./application/views/assets/ */
define('ASSETS_PATH', VIEWS_PATH . 'assets/');

/** Path to ./application/views/assets/img/ */
define('IMG_PATH', ASSETS_PATH . 'img/');

/** Path to ./application/views/assets/css/ */
define('CSS_PATH', ASSETS_PATH . 'css/');

/*
|--------------------------------------------------------------------------
| User rights
|--------------------------------------------------------------------------
|
| Specify the user rights.
|
*/


/**
 * An user who has completed the registration
 * but not confirmed his email address yet,
 * has this rights
 */
define('UNCONFIRMED_ACCOUNT', 1);

/**
 * An user registered has this rights.
 * These are standard user rights.
 */
define('USER_RIGHTS', 2);

/**
 * Admin rights.
 * Who owns these righs can access to
 * all controllers.
 */
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

/** Invalid exception code */
define('INVALID_EXCEPTION_CODE', 0);
/** An invalid parameter was passed to a function */
define('INVALID_PARAMETER', 10001);

/** An ID does not exists in local db */
define('ID_NON_EXISTENT', 10002);
/** An User name does not exists in local db */
define('USER_NAME_NON_EXISTENT', 10003);
/** An email address does not exists in local db */
define('EMAIL_NON_EXISTENT', 10004);
/** An ISBN code does not exists in local db */
define('ISBN_NON_EXISTENT', 10005);
/** A Google ID does not exists in local db */
define('GOOGLE_ID_NON_EXISTENT', 10006);

/** A pair user_id - book_id already exists in sales db */
define('WRONG_CONFIRM_CODE', 10007);

/** A pair user_id - book_id already exists in sales db */
define('EXISTING_SALE', 10008);
/** A pair user_id - book_id already exists in requests db */
define('EXISTING_REQUEST', 10009);

/** An ISBN cannot be found using google API */
define('ISBN_NOT_FOUND', 10010);
/** A book cannot be found */
define('BOOK_NOT_FOUND', 10011);

/* End of file constants.php */
/* Location: ./application/config/constants.php */