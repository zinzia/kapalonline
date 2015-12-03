<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once( BASEPATH.'plugins/excel/PHPExcel.php' );

class CI_Phpexcel extends PHPExcel {

    function CI_Phpexcel() {
        // tcpdf constructor
        parent::__construct();

    }
}
// END PHPExcel Class
