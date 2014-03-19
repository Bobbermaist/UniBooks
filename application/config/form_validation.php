<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config = array(
	'signup/index' => array(
    array(
      'field'   => 'user_name', 
      'label'   => 'Username', 
      'rules'   => 'required|min_length[3]|max_length[20]|xss_clean|is_unique[users.user_name]',
    ),
    array(
      'field'   => 'password', 
      'label'   => 'Password', 
      'rules'   => 'required|min_length[4]|max_length[32]|matches[passconf]',
    ),
    array(
      'field'   => 'passconf', 
      'label'   => 'Password Confirmation', 
      'rules'   => 'required',
    ),
    array(
      'field'   => 'email', 
      'label'   => 'Email', 
      'rules'   => 'required|valid_email|is_unique[users.email]',
    ),
  ),
  'login/index' => array(
    array(
      'field'   => 'user_name',
      'label'   => 'Username',
      'rules'   => 'required|min_length[3]|max_length[20]|xss_clean',
    ),
    array(
      'field'   => 'password',
      'label'   => 'Password',
      'rules'   => 'required|min_length[4]|max_length[32]',
    ),
	),
  'book/index' => array(
    array(
      'field'   => 'search_key',
      'label'   => 'Ricerca libro',
      'rules'   => 'required|valid_isbn',
    ),
  ),
  'sell/index' => array(
    array(
      'field'   => 'isbn',
      'label'   => 'Codice ISBN',
      'rules'   => 'required|valid_isbn',
    ),
    array(
      'field'   => 'price',
      'label'   => 'Prezzo di vendta',
      'rules'   => 'required|valid_price',
    ),
    array(
      'field'   => 'description',
      'label'   => 'Descrizione',
      'rules'   => 'max_length[500]',
    ),
  ),
  'request/index' => array(
    array(
      'field'   => 'book_search',
      'label'   => 'Chiave di ricerca',
      'rules'   => 'valid_isbn',
    ),
  ),
  'sell/choose_price' => array(
    array(
      'field'   => 'price',
      'label'   => 'Book price',
      'rules'   => 'required|valid_price',
    ),
  ),
);

/* End of file form_validation.php */
/* Location: ./application/config/form_validation.php */ 
 
