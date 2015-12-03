<?php

class noSIUPBySlug
{

    /**
     * @var string $slug_perusahaan
     * @access public
     */
    public $slug_perusahaan = null;

    /**
     * @var string $jenis
     * @access public
     */
    public $jenis = null;

    /**
     * @param string $slug_perusahaan
     * @param string $jenis
     * @access public
     */
    public function __construct($slug_perusahaan, $jenis)
    {
      $this->slug_perusahaan = $slug_perusahaan;
      $this->jenis = $jenis;
    }

}
