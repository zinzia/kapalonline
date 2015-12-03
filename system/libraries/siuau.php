<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once( BASEPATH . 'plugins/siuau/SIUAU_WSIService.php' );

class CI_Siuau extends SIUAU_WSIService{
    //put your code here
    function CI_Siuau() {
        // simponi constructor
        parent::__construct();

    }
}
