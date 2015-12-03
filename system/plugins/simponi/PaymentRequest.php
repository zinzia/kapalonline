<?php

class PaymentRequest
{

    /**
     * @var string $appsId
     * @access public
     */
    public $appsId = null;

    /**
     * @var string $invoiceNo
     * @access public
     */
    public $invoiceNo = null;

    /**
     * @var string $routeId
     * @access public
     */
    public $routeId = null;

    /**
     * @var requestData $data
     * @access public
     */
    public $data = null;

    /**
     * @param string $appsId
     * @param string $invoiceNo
     * @param string $routeId
     * @param requestData $data
     * @access public
     */
    public function __construct($appsId, $invoiceNo, $routeId, $data)
    {
      $this->appsId = $appsId;
      $this->invoiceNo = $invoiceNo;
      $this->routeId = $routeId;
      $this->data = $data;
    }

}
