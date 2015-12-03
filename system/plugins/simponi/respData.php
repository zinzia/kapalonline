<?php

class respData
{

    /**
     * @var SimponiTrxId $SimponiTrxId
     * @access public
     */
    public $SimponiTrxId = null;

    /**
     * @var KodeBillingSimponi $KodeBillingSimponi
     * @access public
     */
    public $KodeBillingSimponi = null;

    /**
     * @var string $Date
     * @access public
     */
    public $Date = null;

    /**
     * @var string $ExpiredDate
     * @access public
     */
    public $ExpiredDate = null;

    /**
     * @param SimponiTrxId $SimponiTrxId
     * @param KodeBillingSimponi $KodeBillingSimponi
     * @param string $Date
     * @param string $ExpiredDate
     * @access public
     */
    public function __construct($SimponiTrxId, $KodeBillingSimponi, $Date, $ExpiredDate)
    {
      $this->SimponiTrxId = $SimponiTrxId;
      $this->KodeBillingSimponi = $KodeBillingSimponi;
      $this->Date = $Date;
      $this->ExpiredDate = $ExpiredDate;
    }

}
