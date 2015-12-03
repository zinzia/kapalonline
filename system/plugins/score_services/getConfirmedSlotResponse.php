<?php

class getConfirmedSlotResponse {

    /**
     * @var anyType $confirmedSlots
     * @access public
     */
    public $confirmedSlots = null;

    /**
     * @param anyType $confirmedSlots
     * @access public
     */
    public function __construct($confirmedSlots) {
        $this->confirmedSlots = $confirmedSlots;
    }

}
