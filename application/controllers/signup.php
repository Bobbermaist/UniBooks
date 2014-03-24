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
 * Signup controller.
 *
 * Sign up to Unibooks.
 *
 * @package UniBooks
 * @category Controllers
 * @author Emiliano Bovetti
 */
class Signup extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->database();
        $post = $this->input->post();

        if ($this->form_validation->run() === TRUE)
        {
            $this->User_model->set_user_name($this->input->post('user_name'));
            $this->User_model->set_password($this->input->post('password'));
            $this->User_model->set_email($this->input->post('email'));
            $this->User_model->insert();
            $this->_send_activation();

            $this->_set_message('signup_complete');
        }
        else
        {
            $this->_set_view('form/registration', array(
                'user_name' => array(
                    'name'          => 'user_name',
                    'maxlength' => '20',
                    'value'         => ($post === FALSE) ? '' : $post['user_name'],
                ),
                'email' => array(
                    'name'          => 'email',
                    'maxlength' => '64',
                    'value'         => ($post === FALSE) ? '' : $post['email'],
                ),
                'password' => array(
                    'name'          => 'password',
                    'maxlength' => '64',
                ),
                'passconf'  => array(
                    'name'          => 'passconf',
                    'maxlength' => '64',
                ),
            ));
        }

        $this->_view();
    }

    private function _send_activation()
    {
        $this->load->library('email');

        $this->email->from('registration@unibooks.it');
        $this->email->to($this->User_model->get_email());
        $this->email->subject('Attivazione account');

        $email_data = array(
            'user_name' => $this->User_model->get_user_name(),
            'link'          => $this->User_model->get_confirm_link('activation/index')
        );
        $this->email->message( $this->load->view('email/signup', $email_data, TRUE) );
        $this->email->send();
        echo $this->email->print_debugger();
    }
}

// END Signup class

/* End of file signup.php */
/* Location: ./application/controllers/signup.php */ 
