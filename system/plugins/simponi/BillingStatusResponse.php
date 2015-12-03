<?php

class BillingStatusResponse
{

    /**
     * @var BillingStatusRequest $RequestData
     * @access public
     */
    public $RequestData = null;

    /**
     * @var string $ResponseCode
     * @access public
     */
    public $ResponseCode = null;

    /**
     * @var string $ResponseMessage
     * @access public
     */
    public $ResponseMessage = null;

    /**
     * @var BillingStatusResponseData $ResponseData
     * @access public
     */
    public $ResponseData = null;

    /**
     * @param BillingStatusRequest $RequestData
     * @param string $ResponseCode
     * @param string $ResponseMessage
     * @param BillingStatusResponseData $ResponseData
     * @access public
     */
    public function __construct($RequestData, $ResponseCode, $ResponseMessage, $ResponseData)
    {
      $this->RequestData = $RequestData;
      $this->ResponseCode = $ResponseCode;
      $this->ResponseMessage = $ResponseMessage;
      $this->ResponseData = $ResponseData;
    }

}
