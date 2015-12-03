<?php

class requestData
{

    /**
     * @var PaymentHeaderType $PaymentHeader
     * @access public
     */
    public $PaymentHeader = null;

    /**
     * @var PaymentDetailList $PaymentDetails
     * @access public
     */
    public $PaymentDetails = null;

    /**
     * @param PaymentHeaderType $PaymentHeader
     * @param PaymentDetailList $PaymentDetails
     * @access public
     */
    public function __construct($PaymentHeader, $PaymentDetails)
    {
      $this->PaymentHeader = $PaymentHeader;
      $this->PaymentDetails = $PaymentDetails;
    }

}
