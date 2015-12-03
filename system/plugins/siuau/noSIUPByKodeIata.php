<?php

class noSIUPByKodeIata
{

    /**
     * @var string $kode_iata
     * @access public
     */
    public $kode_iata = null;

    /**
     * @var string $jenis
     * @access public
     */
    public $jenis = null;

    /**
     * @param string $kode_iata
     * @param string $jenis
     * @access public
     */
    public function __construct($kode_iata, $jenis)
    {
      $this->kode_iata = $kode_iata;
      $this->jenis = $jenis;
    }

}
