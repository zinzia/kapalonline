<?php

class BillingStatusResponseData
{

    /**
     * @var SimponiTrxId $SimponiTrxId
     * @access public
     */
    public $SimponiTrxId = null;

    /**
     * @var NTB $NTB
     * @access public
     */
    public $NTB = null;

    /**
     * @var NTPN $NTPN
     * @access public
     */
    public $NTPN = null;

    /**
     * @var string $TrxDate
     * @access public
     */
    public $TrxDate = null;

    /**
     * @var BankPersepsi $BankPersepsi
     * @access public
     */
    public $BankPersepsi = null;

    /**
     * @var ChannelPembayaran $ChannelPembayaran
     * @access public
     */
    public $ChannelPembayaran = null;

    /**
     * @param SimponiTrxId $SimponiTrxId
     * @param NTB $NTB
     * @param NTPN $NTPN
     * @param string $TrxDate
     * @param BankPersepsi $BankPersepsi
     * @param ChannelPembayaran $ChannelPembayaran
     * @access public
     */
    public function __construct($SimponiTrxId, $NTB, $NTPN, $TrxDate, $BankPersepsi, $ChannelPembayaran)
    {
      $this->SimponiTrxId = $SimponiTrxId;
      $this->NTB = $NTB;
      $this->NTPN = $NTPN;
      $this->TrxDate = $TrxDate;
      $this->BankPersepsi = $BankPersepsi;
      $this->ChannelPembayaran = $ChannelPembayaran;
    }

}
