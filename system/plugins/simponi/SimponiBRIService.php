<?php

include_once('PaymentHeader.php');
include_once('PaymentDetail.php');
include_once('PaymentDetailList.php');
include_once('responseData.php');
include_once('PaymentRequest.php');
include_once('PaymentResponse.php');
include_once('requestData.php');
include_once('respData.php');
include_once('BillingStatusRequest.php');
include_once('BillingStatusResponse.php');
include_once('BillingStatusResponse2.php');
include_once('BillingStatusResponseData.php');

class SimponiBRIService extends \SoapClient
{

    /**
     * @var array $classmap The defined classes
     * @access private
     */
    private static $classmap = array(
      'PaymentHeaderType' => '\PaymentHeader',
      'PaymentDetailType' => '\PaymentDetail',
      'PaymentDetailList' => '\PaymentDetailList',
      'responseDataType' => '\responseData',
      'PaymentRequestType' => '\PaymentRequest',
      'PaymentResponseType' => '\PaymentResponse',
      'requestDataType' => '\requestData',
      'respDataType' => '\respData',
      'BillingStatusRequestType' => '\BillingStatusRequest',
      'BillingStatusResponseType' => '\BillingStatusResponse',
      'BillingStatusResponseType2' => '\BillingStatusResponse2',
      'BillingStatusResponseData' => '\BillingStatusResponseData');

    /**
     * @param array $options A array of config values
     * @param string $wsdl The wsdl file to use
     * @access public
     */
    public function __construct(array $options = array(), $wsdl = 'http://soadev.dephub.go.id:7800/SimponiBRI_Service?wsdl')
    {
      foreach (self::$classmap as $key => $value) {
    if (!isset($options['classmap'][$key])) {
      $options['classmap'][$key] = $value;
    }
  }
  
  parent::__construct($wsdl, $options);
    }

    /**
     * @param PaymentRequest $PaymentParameters
     * @access public
     * @return PaymentResponse
     */
    public function PaymentRequest(PaymentRequest $PaymentParameters)
    {
      return $this->__soapCall('PaymentRequest', array($PaymentParameters));
    }

    /**
     * @param BillingStatusRequestType $BillingStatusParameters
     * @access public
     * @return BillingStatusResponseType
     */
    public function BillingStatus(BillingStatusRequest $BillingStatusParameters)
    {
      return $this->__soapCall('BillingStatus', array($BillingStatusParameters));
    }

}
