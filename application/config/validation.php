<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config ['reg_form'] = array(
	array(
		'field'   => 'user_name', 
    'label'   => 'Username', 
    'rules'   => 'trim|required|min_length[4]|max_length[20]xss_clean'
  ),
  array(
    'field'   => 'pass', 
    'label'   => 'Password', 
    'rules'   => 'trim|required|min_length[4]|max_length[32]|matches[passconf]|sha1'
  ),
  array(
  	'field'   => 'passconf', 
    'label'   => 'Password Confirmation', 
    'rules'   => 'trim|required'
  ),
  array(
  	'field'   => 'email', 
    'label'   => 'Email', 
    'rules'   => 'trim|required|valid_email'
  )
);

/* End of file validation.php */
/* Location: ./application/config/validation.php */