<?php

class PaymentHeader
{

    /**
     * @var TrxId $TrxId
     * @access public
     */
    public $TrxId = null;

    /**
     * @var UserId $UserId
     * @access public
     */
    public $UserId = null;

    /**
     * @var Password $Password
     * @access public
     */
    public $Password = null;

    /**
     * @var string $ExpiredDate
     * @access public
     */
    public $ExpiredDate = null;

    /**
     * @var string $DateSent
     * @access public
     */
    public $DateSent = null;

    /**
     * @var KodeKL $KodeKL
     * @access public
     */
    public $KodeKL = null;

    /**
     * @var KodeEselon1 $KodeEselon1
     * @access public
     */
    public $KodeEselon1 = null;

    /**
     * @var KodeSatker $KodeSatker
     * @access public
     */
    public $KodeSatker = null;

    /**
     * @var JenisPNBP $JenisPNBP
     * @access public
     */
    public $JenisPNBP = null;

    /**
     * @var KodeMataUang $KodeMataUang
     * @access public
     */
    public $KodeMataUang = null;

    /**
     * @var TotalNominalBilling $TotalNominalBilling
     * @access public
     */
    public $TotalNominalBilling = null;

    /**
     * @var NamaWajibBayar $NamaWajibBayar
     * @access public
     */
    public $NamaWajibBayar = null;

    /**
     * @param TrxId $TrxId
     * @param UserId $UserId
     * @param Password $Password
     * @param string $ExpiredDate
     * @param string $DateSent
     * @param KodeKL $KodeKL
     * @param KodeEselon1 $KodeEselon1
     * @param KodeSatker $KodeSatker
     * @param JenisPNBP $JenisPNBP
     * @param KodeMataUang $KodeMataUang
     * @param TotalNominalBilling $TotalNominalBilling
     * @param NamaWajibBayar $NamaWajibBayar
     * @access public
     */
    public function __construct($TrxId, $UserId, $Password, $ExpiredDate, $DateSent, $KodeKL, $KodeEselon1, $KodeSatker, $JenisPNBP, $KodeMataUang, $TotalNominalBilling, $NamaWajibBayar)
    {
      $this->TrxId = $TrxId;
      $this->UserId = $UserId;
      $this->Password = $Password;
      $this->ExpiredDate = $ExpiredDate;
      $this->DateSent = $DateSent;
      $this->KodeKL = $KodeKL;
      $this->KodeEselon1 = $KodeEselon1;
      $this->KodeSatker = $KodeSatker;
      $this->JenisPNBP = $JenisPNBP;
      $this->KodeMataUang = $KodeMataUang;
      $this->TotalNominalBilling = $TotalNominalBilling;
      $this->NamaWajibBayar = $NamaWajibBayar;
    }

}
