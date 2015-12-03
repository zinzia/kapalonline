<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
if (!function_exists('numb_to_alphabet')) {

    function numb_to_alphabet($x) {
        $x = abs($x);
        $angka = array("", "satu ", "dua ", "tiga ", "empat ", "lima ",
            "enam ", "tujuh ", "delapan ", "sembilan ", "sepuluh ", "sebelas ");
        $temp = "";
        if ($x < 12) {
            $temp = " " . $angka[$x];
        } else if ($x < 20) {
            $temp = numb_to_alphabet($x - 10) . " belas";
        } else if ($x < 100) {
            $temp = numb_to_alphabet($x / 10) . " puluh" . numb_to_alphabet($x % 10);
        } else if ($x < 200) {
            $temp = " seratus" . numb_to_alphabet($x - 100);
        } else if ($x < 1000) {
            $temp = numb_to_alphabet($x / 100) . " ratus" . numb_to_alphabet($x % 100);
        } else if ($x < 2000) {
            $temp = " seribu" . numb_to_alphabet($x - 1000);
        } else if ($x < 1000000) {
            $temp = numb_to_alphabet($x / 1000) . " ribu" . numb_to_alphabet($x % 1000);
        } else if ($x < 1000000000) {
            $temp = numb_to_alphabet($x / 1000000) . " juta" . numb_to_alphabet($x % 1000000);
        } else if ($x < 1000000000000) {
            $temp = numb_to_alphabet($x / 1000000000) . " milyar" . numb_to_alphabet(fmod($x, 1000000000));
        } else if ($x < 1000000000000000) {
            $temp = numb_to_alphabet($x / 1000000000000) . " trilyun" . numb_to_alphabet(fmod($x, 1000000000000));
        }
        return $temp;
    }

}

if (!function_exists('terbilang')) {

    function terbilang($x) {
        if ($x < 0) {
            $hasil = "minus " . trim(numb_to_alphabet($x));
        } else {
            $poin = trim(tkoma($x));
            $hasil = trim(numb_to_alphabet($x));
            if ($poin) {
                $hasil = ucwords($hasil) . " koma " . $poin;
            } else {
                $hasil = ucwords($hasil);
            }
        }
        return $hasil;
    }

}

function tkoma($x) {
    $x = strstr($x, ".");
    $angka = array("nol", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan");
    $temp = " ";
    $pjg = strlen($x);
    $pos = 1;
    while ($pos < $pjg) {
        $char = substr($x, $pos, 1);
        $pos++;
        $temp .=" " . $angka[$char];
    }
    return $temp;
}

function simply_thousand($number) {
    if ($number >= 1000) {
        return $number / 1000 . "K";   // NB: you will want to round this
    } else {
        return $number;
    }
}

?>
