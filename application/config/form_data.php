<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config = array(
    /* Registration form */
  'signup_data' => array(
  	'user_name_data' => array(
  		'name'			=> 'user_name',
  		'maxlength'	=> '20'/*,
  		'required'	=> TRUE,
  		'autofocus'	=> TRUE*/
  	),
  	'email_data' => array(
  		'name'			=> 'email',
  		'maxlength'	=> '64'/*,
  		'required'	=> TRUE*/
  	),
  	'pass_data' => array(
  		'name'			=> 'pass',
  		'maxlength'	=> '64'/*,
  		'required'	=> TRUE*/
  	),
  	'passconf_data'	=> array(
  		'name'			=> 'passconf',
  		'maxlength'	=> '64'/*,
  		'required'	=> TRUE*/
  	)
  ),
    /* Reset password */
  'reset_data' => array(
  	'reset_form_data' => array(
  		'name'			=> 'user_or_email',
  		'maxlength'	=> '64'
  	)
  ),
  'new_password_data' => array(
  	'new_password_form_data' => array(
  		'name'			=> 'pass',
  		'maxlength'	=> '64'
  	)
  ),
    /* Login form */
  'login_data' => array(
  	'user_name_data' => array(
  		'name'			=> 'user_name',
  		'maxlength'	=> '20'
  	),
  	'pass_data' => array(
  		'name'			=> 'pass',
  		'maxlength'	=> '64'
  	)
  ),
    /* Account change */
  'account_change_data' => array(
    'user_name_data' => array(
      'name'      => 'user_name',
      'maxlength' => '20'
    ),
    'email_data' => array(
      'name'      => 'email',
      'maxlength' => '64'
    ),
    'old_pass_data' => array(
      'name'      => 'old_pass',
      'maxlength' => '64'
    ),
    'new_pass_data' => array(
      'name'      => 'new_pass',
      'maxlength' => '64'
    ),
    'passconf_data' => array(
      'name'      => 'passconf',
      'maxlength' => '64'
    )
  )
);

/* End of file form_data.php */
/* Location: ./application/config/form_data.php */ 
