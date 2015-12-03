<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once( BASEPATH . 'plugins/nusoap/nusoap.php');

class CI_Tsoap extends nusoap_base {
    //put your code here 
    function CI_Tsoap() {
        // tcpdf constructor
        parent::__construct();
    }

}
