<?php

class listPerusahaan
{

    /**
     * @var string $jenis
     * @access public
     */
    public $jenis = null;

    /**
     * @param string $jenis
     * @access public
     */
    public function __construct($jenis)
    {
      $this->jenis = $jenis;
    }

}
