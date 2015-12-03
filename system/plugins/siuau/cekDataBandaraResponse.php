<?php

class cekDataBandaraResponse
{

    /**
     * @var dateTime $cekDataBandaraResult
     * @access public
     */
    public $cekDataBandaraResult = null;

    /**
     * @param dateTime $cekDataBandaraResult
     * @access public
     */
    public function __construct($cekDataBandaraResult)
    {
      $this->cekDataBandaraResult = $cekDataBandaraResult;
    }

}
