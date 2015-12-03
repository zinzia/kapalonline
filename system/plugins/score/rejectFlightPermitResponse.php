<?php
// created by: sanjaya.im@gmail.com
// created on: 7-May-2015
// modified by: sanjaya.im@gmail.com
// modified on: 17-May-2015
class rejectFlightPermitResponse
{

    /**
     * @var anyType $rejectFlightPermitRequest
     * @access public
     */
    public $rejectFlightPermitRequest = null;

    /**
     * @param anyType $rejectFlightRequest
     * @access public
     */
    public function __construct($rejectFlightPermitRequest) {
      $this->rejectFlightPermitRequest = $rejectFlightPermitRequest;
    }
}
