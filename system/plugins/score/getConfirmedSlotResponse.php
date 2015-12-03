<?php
// created by: sanjaya.im@gmail.com
// created on: 7-May-2015
// modified by: sanjaya.im@gmail.com
// modified on: 15-May-2015
// reason modified: add new function to merge some row from SCORE DB base on flightno.
class getConfirmedSlotResponse
{

    /**
     * @var anyType $confirmedSlots
     * @access public
     */
    public $confirmedSlots = null;

    private $parameters = null;

    /**
     * @param anyType $confirmedSlots
     * @access public
     */
    public function __construct($confirmedSlots) {
      $this->confirmedSlots = $confirmedSlots;
    }

    public function getSchedules(getConfirmedSlot $parameters) {
        $this->parameters = $parameters;
        $schedules = $this->confirmedSlots->confirmedScheduleList->confirmedSchedules;
        if (is_object($schedules) && !is_array($schedules)){
            $schedules = array($schedules);
        }
        return $schedules;
    }

    public function getConfirmedSchedules($schedules1, $schedules2, $utc_parse1=0, $utc_parse2=0) {
        // print_r($schedules1);print_r($schedules2);exit;
        $schedules = array();
        if (is_array($schedules1)){
            if (is_array($schedules2)){
                if (count($schedules2) > count($schedules1)){
                    $tmp = $schedules1;
                    $schedules1 = $schedules2;
                    $schedules2 = $tmp;
                }
            }
            foreach ($schedules2 as $k2 => $v2) {
                $schedules2[$k2]->arrivalUsed = false;
                $schedules2[$k2]->departureUsed = false;
            }
            $ii = 0;
            foreach ($schedules1 as $k1 => $v1) {
                $v1 = get_object_vars($v1);
                $is_found = false;
                $dos1 = str_replace("0", "", $v1['doop']);
                $dos1 = str_split($dos1, 1);
                foreach ($schedules2 as $k2 => $v2) {
                    $v2 = get_object_vars($v2);
                    $dos2 = str_replace("0", "", $v2['doop']);
                    $doop = "";
                    foreach ($dos1 as $kk => $vv) {
                        if (strpos($dos2, $vv)!==false){
                            $doop .= $vv;
                        }
                    }
                    $v1['startDate'] = substr($v1['startDate'], 0, 10);
                    $v2['startDate'] = substr($v2['startDate'], 0, 10);
                    $start1 = strtotime($v2['startDate'] . ' -2 day');
                    $start2 = strtotime($v2['startDate'] . ' +2 day');
                    $v1['endDate'] = substr($v1['endDate'], 0, 10);
                    $v2['endDate'] = substr($v2['endDate'], 0, 10);
                    $end1 = strtotime($v2['endDate'] . ' -2 day');
                    $end2 = strtotime($v2['endDate'] . ' +2 day');
                    $startDate = strtotime($v1['startDate']);
                    $endDate = strtotime($v1['endDate']);
                    if ($v1['seasonCode'] == $v2['seasonCode'] && strlen($doop)!=0 && $start1 <= $startDate && $startDate <= $start2 && $end1 <= $endDate && $endDate <= $end2
                        && ((trim($v1['serviceNoArrival']) != "" && trim($v1['serviceNoArrival']) == trim($v2['serviceNoDeparture'])) 
                            || (trim($v1['serviceNoDeparture']) != "" && trim($v1['serviceNoDeparture']) == trim($v2['serviceNoArrival'])))){
                        $is_found = true;
                        if (trim($v1['serviceNoArrival']) != "" && trim($v2['serviceNoDeparture']) != ""){
                            $serviceNo = trim($v1['serviceNoArrival']);
                            $rute = $v1['originStation'] .'-'. $v1['airportCode'];
                            $flight_no = trim($v1['operatorArrival']) . trim($v1['serviceNoArrival']);
                            $schedules[$ii] = array(
                                'seasonCode' => $v1['seasonCode'],
                                'startDate' => $v1['startDate'],
                                'endDate' => $v1['endDate'],
                                'dos' => $v1['doop'],
                                'aircraft_type' => $v1['aircraftType'],
                                'aircraft_capacity' => $v1['numSeats'],
                                'operator' => $v1['operatorArrival'],
                                'ron' => @intval($v1['turnaroundDays']),
                                'serviceNo' => $serviceNo,
                                'rute_all' => $rute,
                                'flight_no' => $flight_no,
                                'eta' => trim($v1['clearedTimeArrival']),
                                'etd' => trim($v2['clearedTimeDeparture'])
                            );
                            $schedules2[$k2]->departureUsed = true;
                            $ii++;
                        }
                        if (trim($v1['serviceNoDeparture']) != "" && trim($v2['serviceNoArrival']) != ""){
                            $serviceNo = trim($v1['serviceNoDeparture']);
                            $rute = $v1['airportCode'] .'-'. $v1['destinationStation'];
                            $flight_no = trim($v1['operatorDeparture']) . trim($v1['serviceNoDeparture']);
                            $schedules[$ii] = array(
                                'seasonCode' => $v1['seasonCode'],
                                'startDate' => $v1['startDate'],
                                'endDate' => $v1['endDate'],
                                'dos' => $v1['doop'],
                                'aircraft_type' => $v1['aircraftType'],
                                'aircraft_capacity' => $v1['numSeats'],
                                'operator' => $v1['operatorDeparture'],
                                'ron' => @intval($v1['turnaroundDays']),
                                'serviceNo' => $serviceNo,
                                'rute_all' => $rute,
                                'flight_no' => $flight_no,
                                'eta' => trim($v2['clearedTimeArrival']),
                                'etd' => trim($v1['clearedTimeDeparture'])
                            );
                            $schedules2[$k2]->arrivalUsed = true;
                            $ii++;
                        }
                    }
                }
                if (!$is_found){
                    if (trim($v1['serviceNoArrival']) != ""){
                        $serviceNo = trim($v1['serviceNoArrival']);
                        $rute = $v1['originStation'] .'-'. $v1['airportCode'];
                        $flight_no = trim($v1['operatorArrival']) . trim($v1['serviceNoArrival']);
                        $schedules[$ii] = array(
                            'seasonCode' => $v1['seasonCode'],
                            'startDate' => $v1['startDate'],
                            'endDate' => $v1['endDate'],
                            'dos' => $v1['doop'],
                            'aircraft_type' => $v1['aircraftType'],
                            'aircraft_capacity' => $v1['numSeats'],
                            'operator' => $v1['operatorArrival'],
                            'ron' => @intval($v1['turnaroundDays']),
                            'serviceNo' => $serviceNo,
                            'rute_all' => $rute,
                            'flight_no' => $flight_no,
                            'eta' => $v1['clearedTimeArrival'],
                            'etd' => ""
                        );
                        $ii++;
                    }
                    if (trim($v1['serviceNoDeparture']) != ""){
                        $serviceNo = trim($v1['serviceNoDeparture']);
                        $rute = $v1['airportCode'] .'-'. $v1['destinationStation'];
                        $flight_no = trim($v1['operatorDeparture']) . trim($v1['serviceNoDeparture']);
                        $schedules[$ii] = array(
                            'seasonCode' => $v1['seasonCode'],
                            'startDate' => $v1['startDate'],
                            'endDate' => $v1['endDate'],
                            'dos' => $v1['doop'],
                            'aircraft_type' => $v1['aircraftType'],
                            'aircraft_capacity' => $v1['numSeats'],
                            'operator' => $v1['operatorDeparture'],
                            'ron' => @intval($v1['turnaroundDays']),
                            'serviceNo' => $serviceNo,
                            'rute_all' => $rute,
                            'flight_no' => $flight_no,
                            'eta' => "",
                            'etd' => $v1['clearedTimeDeparture']
                        );
                        $ii++;
                    }
                }
            }//efor
            foreach ($schedules2 as $k1 => $v1) {
                $v1 = get_object_vars($v1);
                if (!$v1['arrivalUsed'] && trim($v1['serviceNoArrival']) != ""){
                    $serviceNo = trim($v1['serviceNoArrival']);
                    $rute = $v1['originStation'] .'-'. $v1['airportCode'];
                    $flight_no = trim($v1['operatorArrival']) . trim($v1['serviceNoArrival']);
                    $schedules[$ii] = array(
                        'seasonCode' => $v1['seasonCode'],
                        'startDate' => $v1['startDate'],
                        'endDate' => $v1['endDate'],
                        'dos' => $v1['doop'],
                        'aircraft_type' => $v1['aircraftType'],
                        'aircraft_capacity' => $v1['numSeats'],
                        'operator' => $v1['operatorArrival'],
                        'ron' => @intval($v1['turnaroundDays']),
                        'serviceNo' => $serviceNo,
                        'rute_all' => $rute,
                        'flight_no' => $flight_no,
                        'eta' => $v1['clearedTimeArrival'],
                        'etd' => ""
                    );
                    $ii++;
                }
                if (!$v1['departureUsed'] && trim($v1['serviceNoDeparture']) != ""){
                    $serviceNo = trim($v1['serviceNoDeparture']);
                    $rute = $v1['airportCode'] .'-'. $v1['destinationStation'];
                    $flight_no = trim($v1['operatorDeparture']) . trim($v1['serviceNoDeparture']);
                    $schedules[$ii] = array(
                        'seasonCode' => $v1['seasonCode'],
                        'startDate' => $v1['startDate'],
                        'endDate' => $v1['endDate'],
                        'dos' => $v1['doop'],
                        'aircraft_type' => $v1['aircraftType'],
                        'aircraft_capacity' => $v1['numSeats'],
                        'operator' => $v1['operatorDeparture'],
                        'ron' => @intval($v1['turnaroundDays']),
                        'serviceNo' => $serviceNo,
                        'rute_all' => $rute,
                        'flight_no' => $flight_no,
                        'eta' => "",
                        'etd' => $v1['clearedTimeDeparture']
                    );
                    $ii++;
                }
            }//efor
        } else {
            $ii = 0;
            foreach ($schedules2 as $k1 => $v1) {
                $v1 = get_object_vars($v1);
                if (trim($v1['serviceNoArrival']) != ""){
                    $serviceNo = trim($v1['serviceNoArrival']);
                    $rute = $v1['originStation'] .'-'. $v1['airportCode'];
                    $flight_no = trim($v1['operatorArrival']) . trim($v1['serviceNoArrival']);
                    $schedules[$ii] = array(
                        'seasonCode' => $v1['seasonCode'],
                        'startDate' => $v1['startDate'],
                        'endDate' => $v1['endDate'],
                        'dos' => $v1['doop'],
                        'aircraft_type' => $v1['aircraftType'],
                        'aircraft_capacity' => $v1['numSeats'],
                        'operator' => $v1['operatorArrival'],
                        'ron' => @intval($v1['turnaroundDays']),
                        'serviceNo' => $serviceNo,
                        'rute_all' => $rute,
                        'flight_no' => $flight_no,
                        'eta' => $v1['clearedTimeArrival'],
                        'etd' => ""
                    );
                    $ii++;
                }
                if (trim($v1['serviceNoDeparture']) != ""){
                    $serviceNo = trim($v1['serviceNoDeparture']);
                    $rute = $v1['airportCode'] .'-'. $v1['destinationStation'];
                    $flight_no = trim($v1['operatorDeparture']) . trim($v1['serviceNoDeparture']);
                    $schedules[$ii] = array(
                        'seasonCode' => $v1['seasonCode'],
                        'startDate' => $v1['startDate'],
                        'endDate' => $v1['endDate'],
                        'dos' => $v1['doop'],
                        'aircraft_type' => $v1['aircraftType'],
                        'aircraft_capacity' => $v1['numSeats'],
                        'operator' => $v1['operatorDeparture'],
                        'ron' => @intval($v1['turnaroundDays']),
                        'serviceNo' => $serviceNo,
                        'rute_all' => $rute,
                        'flight_no' => $flight_no,
                        'eta' => "",
                        'etd' => $v1['clearedTimeDeparture']
                    );
                    $ii++;
                }
            }
        }
        $scope_start = $parameters->strStartDate;
        $scope_end = $parameters->strEndDate;
        if ($scope_start != "") {
            $scope_start = date("Y-m-d H:i", strtotime($scope_start . ' +1441 minutes'));
            if ($scope_end != "") $scope_end = date("Y-m-d H:i", strtotime($scope_end . ' +1439 minutes'));
        } elseif ($scope_start == "" && $scope_end != "")  $scope_end = date("Y-m-d H:i", strtotime($scope_end . ' +1439 minutes'));
        $tt = $schedules;
        unset($schedules);
        foreach ($tt as $k => $v) {
            $v['score_startDate'] = $v['startDate'] = substr($v['startDate'], 0,10);
            $v['score_endDate'] = $v['endDate'] = substr($v['endDate'], 0,10);
            list($origAirportCode,$destAirportCode) = explode("-",$v['rute_all']);
            if ($this->parameters->origAirportCode != $origAirportCode && $this->parameters->destAirportCode == $destAirportCode){
                $v['rute_all'] = $this->parameters->origAirportCode . "-" . $destAirportCode;
            } elseif ($this->parameters->origAirportCode == $origAirportCode && $this->parameters->destAirportCode != $destAirportCode){
                $v['rute_all'] = $origAirportCode . "-" . $this->parameters->destAirportCode;
            } elseif ($this->parameters->origAirportCode != $destAirportCode && $this->parameters->destAirportCode == $origAirportCode){
                $v['rute_all'] = $origAirportCode . "-" . $this->parameters->origAirportCode;
            } elseif ($this->parameters->origAirportCode == $destAirportCode && $this->parameters->destAirportCode != $origAirportCode){
                $v['rute_all'] = $this->parameters->destAirportCode . "-" . $destAirportCode;
            }
            list($origAirportCode,$destAirportCode) = explode("-",$v['rute_all']);
            $is_have_eta = false;
            $is_have_etd = false;
            if (trim($v['eta'])!=""){
                $v['eta'] = substr(trim($v['eta']),0,2).':'.substr(trim($v['eta']),-2);
                if ($this->parameters->origAirportCode != $origAirportCode){
                    $startTimes = strtotime($v['startDate'] . ' ' . $v['eta'] . ' +'.$utc_parse2.' hour');
                    $endTimes = strtotime($v['endDate'] . ' ' . $v['eta'] . ' +'.$utc_parse2.' hour');
                    $v['utc2'] = $utc_parse2;
                } else {
                    $startTimes = strtotime($v['startDate'] . ' ' . $v['eta'] . ' +'.$utc_parse1.' hour');
                    $endTimes = strtotime($v['endDate'] . ' ' . $v['eta'] . ' +'.$utc_parse1.' hour');
                    $v['utc2'] = $utc_parse1;
                }
                if ($scope_start != "" && $scope_end != ""){
                    if (strtotime($scope_start) > $startTimes) {
                        $ii++;
                        continue;
                    }
                    if (strtotime($scope_end) < $startTimes || strtotime($scope_end) < $endTimes) {
                        $ii++;
                        continue;
                    }
                }
                $startDateETA = $v['startDate'];
                $endDateETA = $v['endDate'];
                $is_have_eta = true;
                $v['startDate'] = $v['izin_start_date'] = date("Y-m-d", $startTimes);
                $v['endDate'] = $v['izin_expired_date'] = date("Y-m-d", $endTimes);
                $v['eta_utc'] = $v['eta'];                
                $v['eta'] = date("H:i:00", $startTimes);
            }
            if (trim($v['etd'])!=""){
                $v['etd'] = substr(trim($v['etd']),0,2).':'.substr(trim($v['etd']),-2);
                if ($this->parameters->destAirportCode != $destAirportCode){
                    $startTimes = strtotime($v['startDate'] . ' ' . $v['etd'] . ' +'.$utc_parse1.' hour');
                    $endTimes = strtotime($v['endDate'] . ' ' . $v['etd'] . ' +'.$utc_parse1.' hour');
                    $v['utc1'] = $utc_parse1;
                } else {
                    $startTimes = strtotime($v['startDate'] . ' ' . $v['etd'] . ' +'.$utc_parse2.' hour');
                    $endTimes = strtotime($v['endDate'] . ' ' . $v['etd'] . ' +'.$utc_parse2.' hour');
                    $v['utc1'] = $utc_parse2;
                }                
                if ($scope_start != "" && $scope_end != ""){
                    if (strtotime($scope_start) > $startTimes) {
                        $ii++;
                        continue;
                    }
                    if (strtotime($scope_end) < $startTimes || strtotime($scope_end) < $endTimes) {
                        $ii++;
                        continue;
                    }
                }
                $startDateETD = $v['startDate'];
                $endDateETD = $v['endDate'];
                $is_have_etd = true;                
                $v['startDate'] = $v['izin_start_date'] = date("Y-m-d", $startTimes);
                $v['endDate'] = $v['izin_expired_date'] = date("Y-m-d", $endTimes);
                $v['etd_utc'] = $v['etd'];
                $v['etd'] = date("H:i:00", $startTimes);
            }
            if($is_have_etd) {
                if ($startDateETD != $v['startDate']){
                    $dos = array("0","0","0","0","0","0","0");
                    $doop = str_split($v['dos'], 1);
                    foreach($doop as $kk => $vv) {
                        $i = $kk + 1;
                        if($i==7) $i=0;
                        if($vv!="0") $dos[$i] = strval($i+1);
                    }
                    $v['dos'] = implode('',$dos);
                }
            } elseif($is_have_eta){
                if ($startDateETA != $v['startDate']){
                    $dos = array("0","0","0","0","0","0","0");
                    $doop = str_split($v['dos'], 1);
                    foreach($doop as $kk => $vv) {
                        $i = $kk + 1;
                        if($i==7) $i=0;
                        if($vv!="0") $dos[$i] = strval($i+1);
                    }
                    $v['dos'] = implode('',$dos);
                }
            }
            $schedules[] = $v;
        }
        $schedules = $this->_array_orderby($schedules, 'serviceNo', SORT_ASC);
        return $schedules;
    }

    private function _array_orderby(){
        $args = func_get_args();
        $data = array_shift($args);
        foreach ($args as $n => $field) {
            if (is_string($field)) {
                $tmp = array();
                foreach ($data as $key => $row)
                    $tmp[$key] = $row[$field];
                $args[$n] = $tmp;
            }
        }
        $args[] = &$data;
        call_user_func_array('array_multisort', $args);
        return array_pop($args);
    }

    private function _set_object_vars($object, array $vars) {
        $has = get_object_vars($object);
        foreach ($has as $name => $oldValue) {
            $object->$name = isset($vars[$name]) ? $vars[$name] : NULL;
        }
    }
}