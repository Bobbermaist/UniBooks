<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config = array(
	'user/registration' => array(
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
  'user/login' => array(
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
  'account/password' => array(
    array(
      'field'   => 'old_pass',
      'label'   => 'Old password',
      'rules'   => 'required'
    ),
    array(
      'field'   => 'new_pass', 
      'label'   => 'New password', 
      'rules'   => 'required|min_length[4]|max_length[32]|matches[passconf]'
    ),
    array(
      'field'   => 'passconf', 
      'label'   => 'Password Confirmation', 
      'rules'   => 'required'
    )
  ),
  'request/index' => array(
    array(
      'field'   => 'book_search',
      'label'   => 'Chiave di ricerca',
      'rules'   => 'valid_ISBN'
    )
  ),
  'sell/choose_price' => array(
    array(
      'field'   => 'price',
      'label'   => 'Book price',
      'rules'   => 'required|valid_price'
    )
  )
);

/* End of file form_validation.php */
/* Location: ./application/config/form_validation.php */ 
 
