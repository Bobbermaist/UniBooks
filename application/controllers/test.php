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
 * UniBooks Test class.
 *
 * @package UniBooks
 * @category Controllers
 * @author Emiliano Bovetti
 */
class Test extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$this->prova(1, 2, 3, 4, 5);
	}

	public function prova($param0, $param1)
	{
		$parameters = array();
		if (func_num_args() > 2)
		{
			for ($i=2; func_num_args() > $i; $i++)
			{
				$parameters[] = func_get_arg($i);
			}
		}
		var_dump($parameters);
	}
}

// END Test class

/* End of file test.php */
/* Location: ./application/controllers/test.php */ 
 
