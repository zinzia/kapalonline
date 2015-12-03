<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once( BASEPATH . 'libraries/Session.php' );

class CI_Tsession extends CI_Session {
    /*
     * Overwrite session set data
     */

    function set_userdata($newdata, $newval = '') {
        $_SESSION[$newdata] = $newval;
    }

    function unset_userdata($newdata) {
        if (isset($_SESSION[$newdata])) {
            unset($_SESSION[$newdata]);
        }
    }

    function userdata($newdata) {
        if (isset($_SESSION[$newdata])) {
            return $_SESSION[$newdata];
        } else {
            return '';
        }
    }

}