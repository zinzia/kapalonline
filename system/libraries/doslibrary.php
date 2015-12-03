<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of doslibrary
 *
 * @author l4mbang
 */
class doslibrary {

    //put your code here
    public function __construct() {
        
    }

    /**
     * DOS with RON
     * @param string dos
     * @param integer RON
     * @return string reverse value
     */
    public function reverse_dos($raw = "", $ron = 0) {
        $string = str_replace("0", "", $raw);
        //$string = $raw;
        $temp = str_split($string);
        $formatted = "";

        foreach ($temp as $i => $val) {
            $x = $val + $ron;
            if ($val != "0") {
                if ($x <= 7) {
                    $val = $x;
                } else if ($x > 7) {
                    $val = $x - 7;
                }
            } else {
                $val = "0";
            }
            $formatted.=$val;
        }

        $arr = str_split($formatted);
        sort($arr);
        $dos = array(1 => "0", 2 => "0", 3 => "0", 4 => "0", 5 => "0", 6 => "0", 7 => "0");
        foreach ($arr as $y) {
            $dos[$y] = $y;
        }
        $result = implode("", $dos);
        return $result;
    }

}
