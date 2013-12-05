<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config = array(

	'signup_rules' => array(
    array(
      'field'   => 'user_name', 
      'label'   => 'Username', 
      'rules'   => 'required|min_length[3]|max_length[20]|xss_clean|is_unique[users.user_name]'
    ),
    array(
      'field'   => 'pass', 
      'label'   => 'Password', 
      'rules'   => 'required|min_length[4]|max_length[32]|matches[passconf]'
    ),
    array(
      'field'   => 'passconf', 
      'label'   => 'Password Confirmation', 
      'rules'   => 'required'
    ),
    array(
      'field'   => 'email', 
      'label'   => 'Email', 
      'rules'   => 'required|valid_email|is_unique[users.email]'
    )
  ),

  'login_rules' => array(
    array(
      'field'   => 'user_name',
      'label'   => 'Username',
      'rules'   => 'required|min_length[3]|max_length[20]|xss_clean'
    ),
    array(
      'field'   => 'pass',
      'label'   => 'Password',
      'rules'   => 'required|min_length[4]|max_length[32]'
    )
  ),

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

  'login_data' => array(
  	'user_name' => array(
  		'name'			=> 'user_name',
  		'maxlength'	=> '20'
  	),
  	'pass' => array(
  		'name'			=> 'pass',
  		'maxlength'	=> '64'
  	)
  )
  
);

/* End of file form_data.php */
/* Location: ./application/config/form_data.php */ 
