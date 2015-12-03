<?php

class PaymentDetail
{

    /**
     * @var NamaWajibBayar $NamaWajibBayar
     * @access public
     */
    public $NamaWajibBayar = null;

    /**
     * @var KodeTarifSimponi $KodeTarifSimponi
     * @access public
     */
    public $KodeTarifSimponi = null;

    /**
     * @var KodePPSimponi $KodePPSimponi
     * @access public
     */
    public $KodePPSimponi = null;

    /**
     * @var string $KodeAkun
     * @access public
     */
    public $KodeAkun = null;

    /**
     * @var TarifPNBP $TarifPNBP
     * @access public
     */
    public $TarifPNBP = null;

    /**
     * @var Volume $Volume
     * @access public
     */
    public $Volume = null;

    /**
     * @var Satuan $Satuan
     * @access public
     */
    public $Satuan = null;

    /**
     * @var TotalTarifPerRecord $TotalTarifPerRecord
     * @access public
     */
    public $TotalTarifPerRecord = null;

    /**
     * @param NamaWajibBayar $NamaWajibBayar
     * @param KodeTarifSimponi $KodeTarifSimponi
     * @param KodePPSimponi $KodePPSimponi
     * @param string $KodeAkun
     * @param TarifPNBP $TarifPNBP
     * @param Volume $Volume
     * @param Satuan $Satuan
     * @param TotalTarifPerRecord $TotalTarifPerRecord
     * @access public
     */
    public function __construct($NamaWajibBayar, $KodeTarifSimponi, $KodePPSimponi, $KodeAkun, $TarifPNBP, $Volume, $Satuan, $TotalTarifPerRecord)
    {
      $this->NamaWajibBayar = $NamaWajibBayar;
      $this->KodeTarifSimponi = $KodeTarifSimponi;
      $this->KodePPSimponi = $KodePPSimponi;
      $this->KodeAkun = $KodeAkun;
      $this->TarifPNBP = $TarifPNBP;
      $this->Volume = $Volume;
      $this->Satuan = $Satuan;
      $this->TotalTarifPerRecord = $TotalTarifPerRecord;
    }

}
