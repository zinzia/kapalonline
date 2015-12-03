<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once( BASEPATH . 'plugins/score_services/SCORE_Service.php' );

class CI_score_services extends SCORE_Service {

    //put your code here
    function CI_score_services() {
        try {
            // simponi constructor
            parent::__construct();
        } catch (Exception $error) {
            // var_dump($error);die;
        }
    }

}

?>