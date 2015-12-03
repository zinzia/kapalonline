<?php

class rejectFlightPermit
{

		/**
     * @var string $airportCode
     * @access public
     */
    public $airportCode = null;

    /**
     * @var string $doop
     * @access public
     */
    public $doop = null;

    /**
     * @var string $seasonCode
     * @access public
     */
    public $seasonCode = null;

    /**
     * @var string $strStartDate
     * @format YYYYMMDD 
     * @access public
     */
    public $strStartDate = null;

    /**
     * @var string $strEndDate
     * @format YYYYMMDD 
     * @access public
     */
    public $strEndDate = null;

    /**
     * @var string $operatorArrival
     * @access public
     */
    public $operatorArrival = null;

    /**
     * @var string $operatorDeparture
     * @access public
     */
    public $operatorDeparture = null;

    /**
     * @var string $serviceNoArrival
     * @access public
     */
    public $serviceNoArrival = null;

    /**
     * @var string $serviceNoDeparture
     * @access public
     */
    public $serviceNoDeparture = null;

    /**
     * @param anyType $rejectFlightPermit
     * @access public
     */
    public function __construct($airportCode, $operatorArrival, $operatorDeparture, $serviceNoArrival, $serviceNoDeparture, $doop, $strStartDate="", $strEndDate="", $seasonCode="")
    {
    	$this->airportCode = $airportCode;
        $this->operatorArrival = $operatorArrival;
        $this->operatorDeparture = $operatorDeparture;
        $this->serviceNoArrival = $serviceNoArrival;
        $this->serviceNoDeparture = $serviceNoDeparture;
    	$this->doop = $doop;
        $this->strStartDate = $strStartDate;
        $this->strEndDate = $strEndDate;
    	$this->seasonCode = $seasonCode;
    }

}
