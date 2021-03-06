<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
 * Debug helper.
 *
 * @package UniBooks
 * @category Helpers
 * @author Emiliano Bovetti
 */

// ------------------------------------------------------------------------

/**
 * It's really annoying when you don't remember where you
 * put yours var_dump()
 *
 * @param mixed  $var any type of var
 * @return void
 * @access public
 */
function var_debug( $var )
{
    $backtrace = debug_backtrace();
    echo "<b>var_debug()</b><br>\n",
                'Caller file: ', $backtrace[0]['file'], "<br>\n",
                'Caller line: ', $backtrace[0]['line'], "<br><br>\n\n";
    var_dump($var);
    echo "<br>\n<b>end</b>\n\n";
}

/* End of file debug_helper.php */
/* Location: ./application/helpers/debug_helper.php */ 
 
