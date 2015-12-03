<?php

include_once('ping.php');
include_once('pingResponse.php');
include_once('cekDataBandara.php');
include_once('cekDataBandaraResponse.php');
include_once('getDataBandara.php');
include_once('getDataBandaraResponse.php');
include_once('listPerusahaan.php');
include_once('listPerusahaanResponse.php');
include_once('noSIUPByKodeIata.php');
include_once('noSIUPByKodeIataResponse.php');
include_once('noSIUPBySlug.php');
include_once('noSIUPBySlugResponse.php');
include_once('tanggalByKodeIata.php');
include_once('tanggalByKodeIataResponse.php');
include_once('tanggalBySlug.php');
include_once('tanggalBySlugResponse.php');
include_once('validasiRuteIataBySlugPerusahaan.php');
include_once('validasiRuteIataBySlugPerusahaanResponse.php');
include_once('validasiRuteIataByKodeIataPerusahaan.php');
include_once('validasiRuteIataByKodeIataPerusahaanResponse.php');
include_once('showRutePerusahaanBySlugPerusahaan.php');
include_once('showRutePerusahaanBySlugPerusahaanResponse.php');
include_once('showRutePerusahaanByKodeIataPerusahaan.php');
include_once('showRutePerusahaanByKodeIataPerusahaanResponse.php');

class SIUAU_WSIService extends \SoapClient
{

    /**
     * @var array $classmap The defined classes
     * @access private
     */
    private static $classmap = array(
      'ping' => '\ping',
      'pingResponse' => '\pingResponse',
      'cekDataBandara' => '\cekDataBandara',
      'cekDataBandaraResponse' => '\cekDataBandaraResponse',
      'getDataBandara' => '\getDataBandara',
      'getDataBandaraResponse' => '\getDataBandaraResponse',
      'listPerusahaan' => '\listPerusahaan',
      'listPerusahaanResponse' => '\listPerusahaanResponse',
      'noSIUPByKodeIata' => '\noSIUPByKodeIata',
      'noSIUPByKodeIataResponse' => '\noSIUPByKodeIataResponse',
      'noSIUPBySlug' => '\noSIUPBySlug',
      'noSIUPBySlugResponse' => '\noSIUPBySlugResponse',
      'tanggalByKodeIata' => '\tanggalByKodeIata',
      'tanggalByKodeIataResponse' => '\tanggalByKodeIataResponse',
      'tanggalBySlug' => '\tanggalBySlug',
      'tanggalBySlugResponse' => '\tanggalBySlugResponse',
      'validasiRuteIataBySlugPerusahaan' => '\validasiRuteIataBySlugPerusahaan',
      'validasiRuteIataBySlugPerusahaanResponse' => '\validasiRuteIataBySlugPerusahaanResponse',
      'validasiRuteIataByKodeIataPerusahaan' => '\validasiRuteIataByKodeIataPerusahaan',
      'validasiRuteIataByKodeIataPerusahaanResponse' => '\validasiRuteIataByKodeIataPerusahaanResponse',
      'showRutePerusahaanBySlugPerusahaan' => '\showRutePerusahaanBySlugPerusahaan',
      'showRutePerusahaanBySlugPerusahaanResponse' => '\showRutePerusahaanBySlugPerusahaanResponse',
      'showRutePerusahaanByKodeIataPerusahaan' => '\showRutePerusahaanByKodeIataPerusahaan',
      'showRutePerusahaanByKodeIataPerusahaanResponse' => '\showRutePerusahaanByKodeIataPerusahaanResponse');

    /**
     * @param array $options A array of config values
     * @param string $wsdl The wsdl file to use
     * @access public
     */
    public function __construct(array $options = array(), $wsdl = 'https://aol.dephub.go.id/siuau/api')
    {
      foreach (self::$classmap as $key => $value) {
    if (!isset($options['classmap'][$key])) {
      $options['classmap'][$key] = $value;
    }
  }
  
  parent::__construct($wsdl, $options);
    }

    /**
     * @param ping $parameters
     * @access public
     * @return pingResponse
     */
    public function ping(ping $parameters)
    {
      return $this->__soapCall('ping', array($parameters));
    }

    /**
     * @param cekDataBandara $parameters
     * @access public
     * @return cekDataBandaraResponse
     */
    public function cekDataBandara(cekDataBandara $parameters)
    {
      return $this->__soapCall('cekDataBandara', array($parameters));
    }

    /**
     * @param getDataBandara $parameters
     * @access public
     * @return getDataBandaraResponse
     */
    public function getDataBandara(getDataBandara $parameters)
    {
      return $this->__soapCall('getDataBandara', array($parameters));
    }

    /**
     * @param listPerusahaan $parameters
     * @access public
     * @return listPerusahaanResponse
     */
    public function listPerusahaan(listPerusahaan $parameters)
    {
      return $this->__soapCall('listPerusahaan', array($parameters));
    }

    /**
     * @param noSIUPByKodeIata $parameters
     * @access public
     * @return noSIUPByKodeIataResponse
     */
    public function noSIUPByKodeIata(noSIUPByKodeIata $parameters)
    {
      return $this->__soapCall('noSIUPByKodeIata', array($parameters));
    }

    /**
     * @param noSIUPBySlug $parameters
     * @access public
     * @return noSIUPBySlugResponse
     */
    public function noSIUPBySlug(noSIUPBySlug $parameters)
    {
      return $this->__soapCall('noSIUPBySlug', array($parameters));
    }

    /**
     * @param tanggalByKodeIata $parameters
     * @access public
     * @return tanggalByKodeIataResponse
     */
    public function tanggalByKodeIata(tanggalByKodeIata $parameters)
    {
      return $this->__soapCall('tanggalByKodeIata', array($parameters));
    }

    /**
     * @param tanggalBySlug $parameters
     * @access public
     * @return tanggalBySlugResponse
     */
    public function tanggalBySlug(tanggalBySlug $parameters)
    {
      return $this->__soapCall('tanggalBySlug', array($parameters));
    }

    /**
     * @param validasiRuteIataBySlugPerusahaan $parameters
     * @access public
     * @return validasiRuteIataBySlugPerusahaanResponse
     */
    public function validasiRuteIataBySlugPerusahaan(validasiRuteIataBySlugPerusahaan $parameters)
    {
      return $this->__soapCall('validasiRuteIataBySlugPerusahaan', array($parameters));
    }

    /**
     * @param validasiRuteIataByKodeIataPerusahaan $parameters
     * @access public
     * @return validasiRuteIataByKodeIataPerusahaanResponse
     */
    public function validasiRuteIataByKodeIataPerusahaan(validasiRuteIataByKodeIataPerusahaan $parameters)
    {
      return $this->__soapCall('validasiRuteIataByKodeIataPerusahaan', array($parameters));
    }

    /**
     * @param showRutePerusahaanBySlugPerusahaan $parameters
     * @access public
     * @return showRutePerusahaanBySlugPerusahaanResponse
     */
    public function showRutePerusahaanBySlugPerusahaan(showRutePerusahaanBySlugPerusahaan $parameters)
    {
      return $this->__soapCall('showRutePerusahaanBySlugPerusahaan', array($parameters));
    }

    /**
     * @param showRutePerusahaanByKodeIataPerusahaan $parameters
     * @access public
     * @return showRutePerusahaanByKodeIataPerusahaanResponse
     */
    public function showRutePerusahaanByKodeIataPerusahaan(showRutePerusahaanByKodeIataPerusahaan $parameters)
    {
      return $this->__soapCall('showRutePerusahaanByKodeIataPerusahaan', array($parameters));
    }

}
