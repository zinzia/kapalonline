<?php

class tanggalBySlugResponse
{

    /**
     * @var string $tanggalBySlugResult
     * @access public
     */
    public $tanggalBySlugResult = null;

    /**
     * @param string $tanggalBySlugResult
     * @access public
     */
    public function __construct($tanggalBySlugResult)
    {
      $this->tanggalBySlugResult = $tanggalBySlugResult;
    }

}
