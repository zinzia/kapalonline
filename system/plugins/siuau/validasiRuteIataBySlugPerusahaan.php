<?php

class validasiRuteIataBySlugPerusahaan
{

    /**
     * @var string $slug_perusahaan
     * @access public
     */
    public $slug_perusahaan = null;

    /**
     * @var string $kode_iata_airport_asal
     * @access public
     */
    public $kode_iata_airport_asal = null;

    /**
     * @var ArrayCustom $kode_iata_airport_tujuan
     * @access public
     */
    public $kode_iata_airport_tujuan = null;

    /**
     * @var string $jenis
     * @access public
     */
    public $jenis = null;

    /**
     * @var string $kategori
     * @access public
     */
    public $kategori = null;

    /**
     * @var string $sifat
     * @access public
     */
    public $sifat = null;

    /**
     * @param string $slug_perusahaan
     * @param string $kode_iata_airport_asal
     * @param ArrayCustom $kode_iata_airport_tujuan
     * @param string $jenis
     * @param string $kategori
     * @param string $sifat
     * @access public
     */
    public function __construct($slug_perusahaan, $kode_iata_airport_asal, $kode_iata_airport_tujuan, $jenis, $kategori, $sifat)
    {
      $this->slug_perusahaan = $slug_perusahaan;
      $this->kode_iata_airport_asal = $kode_iata_airport_asal;
      $this->kode_iata_airport_tujuan = $kode_iata_airport_tujuan;
      $this->jenis = $jenis;
      $this->kategori = $kategori;
      $this->sifat = $sifat;
    }

}
