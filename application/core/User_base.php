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
 * User_base class.
 *
 * Is extended by User_model class and
 * contains all user properties and
 * base method to manage an user.
 *
 * @package UniBooks
 * @category Base Models
 * @author Emiliano Bovetti
 */
class User_base extends MY_Model {

    /**
     * User ID.
     *
     * @var int
     * @access protected
     */
    protected $ID;

    /**
     * User name.
     *
     * @var string
     * @access protected
     */
    protected $user_name;

    /**
     * Hashed password
     *
     * @var string
     * @access protected
     */
    protected $password;

    /**
     * User email
     *
     * @var string
     * @access protected
     */
    protected $email;

    /**
     * Timestamp
     *
     * @var string
     * @access protected
     */
    protected $registration_time;

    /**
     * User rights
     *
     * @var int
     * @access protected
     */
    protected $rights;

    /**
     * Random string to activate / reset account settings
     *
     * @var string
     * @access protected
     */
    protected $confirm_code;

    /**
     * Email address not confirmed
     *
     * @var string
     * @access protected
     */
    protected $tmp_email;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get ID method.
     *
     * @return int
     */
    public function get_ID()
    {
        return $this->ID;
    }

    /**
     * Set ID method.
     * 
     * @param int  $value the ID value to set
     * @return void
     */
    public function set_ID($value)
    {
        $this->ID = (int) $value;
    }

    /**
     * Get user name.
     *
     * @return string
     */
    public function get_user_name()
    {
        return $this->user_name;
    }

    /**
     * Set user name.
     *
     * Trim and sets user_name property.
     *
     * @param string  $value the user name to set
     * @return void
     */
    public function set_user_name($value)
    {
        $this->user_name = trim($value);
    }

    /**
     * Get password.
     *
     * NOTE: The password property is the *hashed* password.
     *
     * @return string
     */
    public function get_password()
    {
        return $this->password;
    }

    /**
     * Set password.
     *
     * Hash and sets password.
     *
     * @param string  $value the password (not hashed) to set
     * @return void
     */
    public function set_password($value)
    {
        $this->load->helper('security');
        $this->password = do_hash($value);
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function get_email()
    {
        return $this->email;
    }

    /**
     * Set email.
     *
     * Sets email with $value to lower case and trim.
     *
     * @param string  $value the email address to set
     * @return void
     */
    public function set_email($value)
    {
        $this->email = utf8_strtolower(trim($value));
    }

    /**
     * Get registration time.
     *
     * @return string
     */
    public function get_registration_time()
    {
        return $this->registration_time;
    }

    /**
     * Get rights.
     *
     * @return int
     */
    public function get_rights()
    {
        return $this->rights;
    }

    /**
     * Get confirm_code
     *
     * @return string
     */
    public function get_confirm_code()
    {
        return $this->confirm_code;
    }

    /**
     * Get temporary email.
     *
     * @return string
     */
    public function get_tmp_email()
    {
        return $this->tmp_email;
    }

    /**
     * Set temporary email.
     *
     * @param string  $value the email to confirm
     * @return void
     */
    public function set_tmp_email($value)
    {
        $this->email = utf8_strtolower(trim($value));
    }

    /**
     * Unset all object properties
     *
     * @return void
     */
    public function unset_all()
    {
        unset(
            $this->ID,
            $this->user_name,
            $this->password,
            $this->email,
            $this->registration_time,
            $this->rights,
            $this->confirm_code,
            $this->tmp_email
        );
    }

    /**
     * Set confirm code.
     * Generates a random string with the CI string helper
     *
     * @return void
     * @access protected
     */
    protected function _set_confirm_code()
    {
        $this->load->helper('string');
        $this->confirm_code = random_string('alnum', 15);
    }

    /**
     * Sets registration_time with $_SERVER['REQUEST_TIME']
     *
     * @return void
     * @access protected
     */
    protected function _set_time()
    {
        $this->registration_time = date(
            $this->config->item('log_date_format'), 
            $_SERVER['REQUEST_TIME']
        );
    }

    /**
     * Select all user fields from a property indicated 
     * in $field (default 'ID')
     *
     * The field indicated must be a unique value
     * (ID, user_name, email) and corresponding object 
     * property should be setted.
     *
     * Throws an exeption on failure.
     *
     * @param string  $field the field name
     * @return void
     * @throws Custom_exception(REQUIRED_PROPERTY) if
     *    $this->{$field} is not setted
     * @throws Custom_exception(ID_NON_EXISTENT) if the
     *    ID does not exists
     * @throws Custom_exception(USER_NAME_NON_EXISTENT) if the
     *    user name does not exists
     * @throws Custom_exception(EMAIL_NON_EXISTENT) if the
     *    email address dows not exists
     * @throws Custom_exception(INVALID_PARAMETER) if
     *    the parameter provided does not match with valid ones
     */
    public function select_by($field = 'ID')
    {
        if ( ! isset($this->{$field}))
        {
            throw new Custom_exception(REQUIRED_PROPERTY, $field);
        }

        $this->db->from('users')->where($field, $this->{$field})->limit(1);
        $res = $this->db->get();

        if ($res->num_rows() === 0)
        {
            switch ($field)
            {
                case 'ID':
                    throw new Custom_exception(ID_NON_EXISTENT);
                    break;
                case 'user_name':
                    throw new Custom_exception(USER_NAME_NON_EXISTENT);
                    break;
                case 'email':
                    throw new Custom_exception(EMAIL_NON_EXISTENT);
                    break;
                default:
                    throw new Custom_exception(INVALID_PARAMETER);
                    break;
            }
        }

        $user_data = $res->row();
        $this->ID = (int) $user_data->ID;
        $this->user_name = $user_data->user_name;
        $this->password = $user_data->password;
        $this->email = $user_data->email;
        $this->registration_time = $user_data->registration_time;
        $this->rights = (int) $user_data->rights;
    }

    /**
     * Retrieves all object properties from the session data
     *
     * @return boolean
     */
    public function read_session()
    {
        $this->load->library('session');
        if ( ! isset($this->ID))
        {
            $userdata_id = $this->session->userdata('user_id');

            if ($userdata_id === FALSE)
            {
                return FALSE;
            }
            $this->set_id($userdata_id);
            $this->select_by('ID');
        }
        // ID property is setted, return TRUE
        return TRUE;
    }
}

// END User_base class 

/* End of file User_base.php */
/* Location: ./application/core/User_base.php */  