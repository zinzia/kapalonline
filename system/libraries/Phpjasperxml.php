<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once( BASEPATH.'plugins/phpjasperxml/PHPJasperXML.php' );

class CI_Phpjasperxml extends PHPJasperXML {

    function CI_Phpjasperxml($params = array("lang" => "en", "pdflib" => "FPDF")) {
        $lang = "en";
        $pdflib = "FPDF";
        // default language
        if(isset($params['lang'])){
            $lang = $params['lang'];
        }
        // default library
        if(isset($params['pdflib'])){
            $pdflib = $params['pdflib'];
        }
        // tcpdf constructor
        parent::__construct($lang, $pdflib);

    }
}
// END PHPExcel Class
