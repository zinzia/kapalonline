<?php

class BillingStatusResponse2
{

    /**
     * @var BillingStatusRequest $RequestData
     * @access public
     */
    public $RequestData = null;

    /**
     * @var BillingStatusResponseData $ResponseData
     * @access public
     */
    public $ResponseData = null;

    /**
     * @param BillingStatusRequest $RequestData
     * @param BillingStatusResponseData $ResponseData
     * @access public
     */
    public function __construct($RequestData, $ResponseData)
    {
      $this->RequestData = $RequestData;
      $this->ResponseData = $ResponseData;
    }

}
