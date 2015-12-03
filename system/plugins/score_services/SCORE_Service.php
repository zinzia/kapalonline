<?php

include_once('getConfirmedSlot.php');
include_once('getConfirmedSlotResponse.php');

class SCORE_Service extends SoapClient {

    /**
     * @var array $classmap The defined classes
     * @access private
     */
    private static $classmap = array(
        'getConfirmedSlot' => 'getConfirmedSlot',
        'getConfirmedSlotResponse' => 'getConfirmedSlotResponse',
    );
    private static $local_wsdl = "http://10.47.210.17:7800/soa/hubud/pdc/service/PDCKemenHubServiceWS?wsdl";
    private static $public_wsdl = "http://202.61.105.185:7800/soa/hubud/pdc/service/PDCKemenHubServiceWS?wsdl";

    /**
     * @param array $options A array of config values
     * @param string $wsdl The wsdl file to use
     * @access public
     */
    public function __construct(array $options = array()) {
        foreach (self::$classmap as $key => $value) {
            if (!isset($options['classmap'][$key])) {
                $options['classmap'][$key] = $value;
            }
        }
        parent::__construct(self::$local_wsdl, $options);
    }

    /**
     * @param getConfirmedSlot $parameters
     * @access public
     * @return getConfirmedSlotResponse
     */
    public function getConfirmedSlotSeasonal(getConfirmedSlot $parameters) {
        return $this->__soapCall('getConfirmedSlotSeasonal', array($parameters));
    }

}
