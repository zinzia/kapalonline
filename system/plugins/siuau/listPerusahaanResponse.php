<?php

class listPerusahaanResponse
{

    /**
     * @var anyType $listPerusahaanResult
     * @access public
     */
    public $listPerusahaanResult = null;

    /**
     * @param anyType $listPerusahaanResult
     * @access public
     */
    public function __construct($listPerusahaanResult)
    {
      $this->listPerusahaanResult = $listPerusahaanResult;
    }

}
