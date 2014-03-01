<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config = array(
  'new_password_data' => array(
  	'new_password_form_data' => array(
  		'name'			=> 'pass',
  		'maxlength'	=> '64'
  	)
  ),
    /* Account change */


  'change_email_data' => array(
    'redirect' => 'account/email',
    'title' => 'Modifica Email',
    'submit_name' => 'change',
    'submit_value' => 'Modifica',
    'input_type' => array(
      'name'      => 'email',
      'maxlength' => '64'
    )
  ),

  'change_password_data' => array(
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
