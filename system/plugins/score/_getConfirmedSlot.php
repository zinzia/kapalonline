<?php

class getConfirmedSlot
{

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
     * @var string $strStartDate
     * @format YYYYMMDD 
     * @access public
     */
    public $strStartDate = "";

    /**
     * @var string $strEndDate
     * @format YYYYMMDD 
     * @access public
     */
    public $strEndDate = "";

    /**
     * @var string $serviceType
     * @access public
     */
    public $serviceType = "";

    /**
     * @param anyType $getConfirmedSlot
     * @access public
     */
    public function __construct($airlineID, $origAirportCode, $destAirportCode, $strStartDate="", $strEndDate="", $serviceType="J")
    {
    	$this->airlineID = $airlineID;
    	$this->destAirportCode = $destAirportCode;
    	$this->origAirportCode = $origAirportCode;
        $this->strStartDate = $strStartDate;
        $this->strEndDate = $strEndDate;
        if ($serviceType=="*") $serviceType="";
    	$this->serviceType = $serviceType;
    }

}
