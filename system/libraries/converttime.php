<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of convertutc
 *
 * @author EsKa
 */
class converttime {

    //put your code here
    public function __construct() {
    }

    /**
     * DOS with RON
     * @param string time lt
     * @param string time utc
     * @return string time utc
     */
    public function convert_utc($soa_lt = "", $airport_lt = "", $airport_utc_sign = "") {
        // convert soa local time to minutes
        $split = preg_split("/[:]+/", $soa_lt);
        $minutes = ($split[0] * 60) + $split[1];

        // convert to minutes
        $split_utc = preg_split("/[:]+/", $airport_lt);
        $minutes_utc = ($split_utc[0] * 60) + $split_utc[1];
        if ($airport_utc_sign == "+") {
            $temp = ($minutes - $minutes_utc);
        } else {
            $temp = ($minutes + $minutes_utc);
        }
        if ($temp < 0) {
            $temp = 1440 - (abs($temp));
            $hour = floor($temp / 60);
            $minutes = $temp % 60;
        } else {
            $hour = floor($temp / 60);
            $minutes = $temp % 60;
        }
        // convert hour and minutes to 2 digit
        $hour = (strlen($hour) < 2) ? "0" . $hour : $hour;
        $minutes = (strlen($minutes) < 2) ? "0" . $minutes : $minutes;
        // format result then return
        $result = $hour . ":" . $minutes;
        return $result;
    }

}
