<?php

class showRutePerusahaanBySlugPerusahaan
{

    /**
     * @var string $slug_perusahaan
     * @access public
     */
    public $slug_perusahaan = null;

    /**
     * @param string $slug_perusahaan
     * @access public
     */
    public function __construct($slug_perusahaan)
    {
      $this->slug_perusahaan = $slug_perusahaan;
    }

}
