<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * UniBooks
 *
 * An application for books trade off
 *
 * @package UniBooks
 * @author Emiliano Bovetti
 * @since Version 1.0
 */

/**
 * UniBooks Admin class.
 *
 * @package UniBooks
 * @category Controllers
 * @author Emiliano Bovetti
 */
class Admin extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->_restrict_area(ADMIN_RIGHTS, 'admin/index');
    }

    public function index()
    {
        $this->_set_view('generic', array(
            'p' => 'Benvenuto, amministratore <b>' . $this->User_model->user_name() . '</b>!'
        ));

        $this->_view();
    }
}

// END Admin class

/* End of file admin.php */
/* Location: ./application/controllers/admin.php */ 
 
