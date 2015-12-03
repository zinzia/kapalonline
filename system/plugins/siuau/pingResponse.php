<?php

class pingResponse
{

    /**
     * @var boolean $pingResult
     * @access public
     */
    public $pingResult = null;

    /**
     * @param boolean $pingResult
     * @access public
     */
    public function __construct($pingResult)
    {
      $this->pingResult = $pingResult;
    }

}
