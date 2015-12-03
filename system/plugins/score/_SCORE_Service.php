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
        $request1 = $this->__soapCall('getConfirmedSlot', array($parameters));
        $schedules1 = $request1->getConfirmedSchedules($utc_parse1);        
        $tmp = $parameters->destAirportCode;
        $parameters->destAirportCode = $parameters->origAirportCode;
        $parameters->origAirportCode = $tmp;
        $request2 = $this->__soapCall('getConfirmedSlot', array($parameters));
        $schedules2 = $request2->getConfirmedSchedules($utc_parse2);        
        if (is_array($schedules1)){
            $schedules = $schedules1;
            foreach ($schedules1 as $k => $v) {
                if (!isset($schedules2[$k])) continue;
                foreach ($v as $k2 => $v2) {
                    if (!isset($schedules2[$k][$k2])) continue;
                    $t = $schedules2[$k][$k2];
                    if ($t['etd'] != "") {
                        $schedules[$k][$k2]['etd'] = $t['etd'];
                        $schedules[$k][$k2]['etd_utc'] = $t['etd_utc'];
                    }
                    if ($t['eta'] != "") {
                        $schedules[$k][$k2]['eta'] = $t['eta'];
                        $schedules[$k][$k2]['eta_utc'] = $t['eta_utc'];
                    }
                }                
            }        
            if (@intval($filter['frekuensi']) > 0){
                $schedules1 = $schedules;
                unset($schedules);            
                foreach ($schedules1 as $k => $v) {
                    foreach ($v as $k2 => $v2) {
                        $frek = 0;
                        $dos = str_split($v2['dos'], 1);
                        for ($x=0; $x < count($dos); $x++)
                            if ($dos[$x]!="0") $frek++;
                        if ($frek > $filter['frekuensi']) continue;
                        $schedules[] = $v2;
                    }
                }
            }            
        } else $schedules = $schedules2;
        $schedules_tmp = $schedules;
        unset($schedules);
        foreach ($schedules_tmp as $k1 => $v1) {
            foreach ($v1 as $k2 => $v2) {
                $schedules[] = $v2;
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
        $schedules_tmp = $schedules;
        unset($schedules);
        foreach ($schedules_tmp as $k1 => $v1) {
            foreach ($v1 as $k2 => $v2) {
                $schedules[] = $v2;
            }
        }
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
        $schedules_tmp = $schedules;
        unset($schedules);
        foreach ($schedules_tmp as $k1 => $v1) {
            foreach ($v1 as $k2 => $v2) {
                $schedules[] = $v2;
            }
        }
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