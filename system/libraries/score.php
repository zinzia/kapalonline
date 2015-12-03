<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once( BASEPATH . 'plugins/score/SCORE_Service.php' );

class CI_Score extends SCORE_Service{
    //put your code here
    function CI_Score() {
    	try{
    		// simponi constructor
        parent::__construct();
      } catch (Exception $error) {
      	// var_dump($error);die;
      }
    }
}
?>