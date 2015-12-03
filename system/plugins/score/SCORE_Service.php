<?php

include_once('getConfirmedSlot.php');
include_once('getConfirmedSlotResponse.php');
include_once('rejectFlightPermit.php');
include_once('rejectFlightPermitResponse.php');

class SCORE_Service extends \SoapClient
{

    /**
     * @var array $classmap The defined classes
     * @access private
     */
    private static $classmap = array(
      'getConfirmedSlot' => '\getConfirmedSlot',
      'getConfirmedSlotResponse' => '\getConfirmedSlotResponse',
      'rejectFlightPermit' => '\rejectFlightPermit',
      'rejectFlightPermitResponse' => '\rejectFlightPermitResponse');

    private static $local_wsdl = "http://10.47.210.17:7800/soa/hubud/pdc/service/PDCKemenHubServiceWS?wsdl";
    private static $public_wsdl = "http://202.61.105.185:7800/soa/hubud/pdc/service/PDCKemenHubServiceWS?wsdl";

    /**
     * @param array $options A array of config values
     * @param string $wsdl The wsdl file to use
     * @access public
     */
    public function __construct(array $options = array())
    {
			foreach (self::$classmap as $key => $value) {
				if (!isset($options['classmap'][$key])) {
					$options['classmap'][$key] = $value;
				}
			}
			parent::__construct(self::$local_wsdl, $options);
    }

    /**
     * @param rejectFlightPermit $parameters
     * @access public
     * @return rejectFlightPermitResponse
     */
    public function rejectFlightPermit(rejectFlightPermit $parameters)
    {
      return $this->__soapCall('rejectFlightPermit', array($parameters));
    }

    /**
     * @param getConfirmedSlot $parameters
     * @access public
     * @return getConfirmedSlotResponse
     */
    public function getConfirmedSlot(getConfirmedSlot $parameters)
    {
      return $this->__soapCall('getConfirmedSlot', array($parameters));
    }

    /**
     * @param getConfirmedFullSlot $parameters
     * @access public
     * @return getConfirmedFullSlot
     */
    public function getConfirmedFullSlot(getConfirmedSlot $parameters, $utc_parse1=0, $utc_parse2=0, $filter=array())
    {
        $startDate = $parameters->strStartDate;
        $endDate = $parameters->strEndDate;
        if ($startDate != "") $parameters->strStartDate = date("Y-m-d", strtotime($startDate . ' -1 day'));
        elseif ($startDate == "" && $endDate != "") $parameters->strEndDate = date("Y-m-d", strtotime($endDate . ' -1 day'));
        $request1 = $this->__soapCall('getConfirmedSlot', array($parameters));
        $schedules1 = $request1->getSchedules($parameters);
        $tmp = $parameters->destAirportCode;
        $parameters->destAirportCode = $parameters->origAirportCode;
        $parameters->origAirportCode = $tmp;
        $request2 = $this->__soapCall('getConfirmedSlot', array($parameters));
        $schedules2 = $request2->getSchedules($parameters);
        $schedules = $request1->getConfirmedSchedules($schedules1, $schedules2, $utc_parse1, $utc_parse2);
        if (@intval($filter['frekuensi']) > 0){
          $schedules_tmp = $schedules;
          unset($schedules);
          foreach ($schedules_tmp as $k1 => $v1) {
            $frek = 0;
            $dos = str_split($v1['dos'], 1);
            for ($x=0; $x < count($dos); $x++)
                if ($dos[$x]!="0") $frek++;
            if ($frek > $filter['frekuensi']) continue;
            $schedules[] = $v1;
          }
        }
        return $schedules;
    }

    /**
     * @param checkAvailabilitySchedules $parameters
     * @access public
     * @return checkAvailabilitySchedules
     */
    public function checkAvailabilitySchedules(getConfirmedSlot $parameters, $utc_parse1=0, $utc_parse2=0, $aircraft_type, $dos, $flight_no, $etd, $eta, $izin_start_date, $izin_expired_date)
    {
        $is_found = false;        
        $schedules = $this->getConfirmedFullSlot($parameters, $utc_parse1, $utc_parse2);
        $izin_start_date = date('Y-m-d', strtotime($izin_start_date));
        $izin_expired_date = date('Y-m-d', strtotime($izin_expired_date));        
        foreach ($schedules as $k => $v) {
            $startDate = date('Y-m-d', strtotime($v['startDate']));
            $endDate = date('Y-m-d', strtotime($v['endDate']));
            if ($v['flight_no'] == $flight_no && $v['dos'] == $dos && $v['etd'] == $etd && $v['eta'] == $eta
                && $endDate > $izin_start_date && $endDate < $izin_expired_date){
                $is_found = true;
                break;
            }
        }
        return $is_found;
    }

    /**
     * @param checkAvailabilityFASchedules $parameters
     * @access public
     * @return checkAvailabilityFASchedules
     */
    public function checkAvailabilityFASchedules(getConfirmedSlot $parameters, $utc_parse1=0, $utc_parse2=0, $aircraft_type, $flight_no, $etd, $eta, $izin_start_date, $izin_expired_date)
    {
        $is_found = false;
        $schedules = $this->getConfirmedFullSlot($parameters, $utc_parse1, $utc_parse2);
        $izin_start_date = date('Y-m-d', strtotime($izin_start_date));
        $izin_expired_date = date('Y-m-d', strtotime($izin_expired_date));
        foreach ($schedules as $k => $v) {
            $startDate = date('Y-m-d', strtotime($v['startDate']));
            $endDate = date('Y-m-d', strtotime($v['endDate']));
            if ($v['flight_no'] == $flight_no && $v['etd'] == $etd && $v['eta'] == $eta
                && $endDate > $izin_start_date && $endDate < $izin_expired_date){
                $is_found = true;
                break;
            }
        }
        return $is_found;
    }
}
?>