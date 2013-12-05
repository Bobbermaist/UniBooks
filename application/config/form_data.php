<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config = array(
  'signup' => array(
  	'user_name_data' => array(
  		'name'			=> 'user_name',
  		'maxlength'	=> '20',
  		'required'	=> TRUE,
  		'autofocus'	=> TRUE
  	),
  	'email_data' => array(
  		'name'			=> 'email',
  		'maxlength'	=> '64',
  		'required'	=> TRUE
  	),
  	'pass_data' => array(
  		'name'			=> 'pass',
  		'maxlength'	=> '64',
  		'required'	=> TRUE
  	),
  	'passconf_data'	=> array(
  		'name'			=> 'passconf',
  		'maxlength'	=> '64',
  		'required'	=> TRUE
  	)
  )
);

/* End of file form_data.php */
/* Location: ./application/config/form_data.php */ 
