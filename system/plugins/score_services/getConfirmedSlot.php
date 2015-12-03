<?php

class getConfirmedSlot {

    /**
     * @var string $airlineID
     * @access public
     */
    public $airlineID = "";

    /**
     * @var string $destAirportCode
     * @access public
     */
    public $destAirportCode = "";

    /**
     * @var string $origAirportCode
     * @access public
     */
    public $origAirportCode = "";

    /**
     * @var string $serviceType
     * @access public
     */
    public $serviceType = "";

    /**
     * @var string $strSeasonCode
     * @access public
     */
    public $strSeasonCode = "";

    /**
     * @var string $strServiceNo
     * @access public
     */
    public $strServiceNo = "";

    /**
     * @param anyType $getConfirmedSlot
     * @access public
     */
    public function __construct($airlineID, $origAirportCode, $destAirportCode, $strSeasonCode, $serviceType = "", $strServiceNo = '') {
        $this->airlineID = $airlineID;
        $this->destAirportCode = $destAirportCode;
        $this->origAirportCode = $origAirportCode;
        $this->strSeasonCode = $strSeasonCode;
        if ($serviceType == "*")
            $serviceType = "";
        $this->serviceType = $serviceType;
        $this->strServiceNo = $strServiceNo;
    }

}
