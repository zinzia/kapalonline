<?php

class BillingStatusRequest
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
     * @var TrxId $TrxId
     * @access public
     */
    public $TrxId = null;

    /**
     * @var UserId $UserId
     * @access public
     */
    public $UserId = null;

    /**
     * @var Password $Password
     * @access public
     */
    public $Password = null;

    /**
     * @var KodeBillingSimponi $KodeBillingSimponi
     * @access public
     */
    public $KodeBillingSimponi = null;

    /**
     * @var KodeKL $KodeKL
     * @access public
     */
    public $KodeKL = null;

    /**
     * @var KodeEselon1 $KodeEselon1
     * @access public
     */
    public $KodeEselon1 = null;

    /**
     * @var KodeSatker $KodeSatker
     * @access public
     */
    public $KodeSatker = null;

    /**
     * @param string $appsId
     * @param string $invoiceNo
     * @param string $routeId
     * @param TrxId $TrxId
     * @param UserId $UserId
     * @param Password $Password
     * @param KodeBillingSimponi $KodeBillingSimponi
     * @param KodeKL $KodeKL
     * @param KodeEselon1 $KodeEselon1
     * @param KodeSatker $KodeSatker
     * @access public
     */
    public function __construct($appsId, $invoiceNo, $routeId, $TrxId, $UserId, $Password, $KodeBillingSimponi, $KodeKL, $KodeEselon1, $KodeSatker)
    {
      $this->appsId = $appsId;
      $this->invoiceNo = $invoiceNo;
      $this->routeId = $routeId;
      $this->TrxId = $TrxId;
      $this->UserId = $UserId;
      $this->Password = $Password;
      $this->KodeBillingSimponi = $KodeBillingSimponi;
      $this->KodeKL = $KodeKL;
      $this->KodeEselon1 = $KodeEselon1;
      $this->KodeSatker = $KodeSatker;
    }

}
