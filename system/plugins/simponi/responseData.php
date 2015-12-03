<?php

class responseData
{

    /**
     * @var string $code
     * @access public
     */
    public $code = null;

    /**
     * @var string $message
     * @access public
     */
    public $message = null;

    /**
     * @var respDataType $simponiData
     * @access public
     */
    public $simponiData = null;

    /**
     * @param string $code
     * @param string $message
     * @param respDataType $simponiData
     * @access public
     */
    public function __construct($code, $message, $simponiData)
    {
      $this->code = $code;
      $this->message = $message;
      $this->simponiData = $simponiData;
    }

}
