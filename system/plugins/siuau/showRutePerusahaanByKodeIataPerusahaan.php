<?php

class showRutePerusahaanByKodeIataPerusahaan
{

    /**
     * @var string $kode_iata_perusahaan
     * @access public
     */
    public $kode_iata_perusahaan = null;

    /**
     * @param string $kode_iata_perusahaan
     * @access public
     */
    public function __construct($kode_iata_perusahaan)
    {
      $this->kode_iata_perusahaan = $kode_iata_perusahaan;
    }

}
