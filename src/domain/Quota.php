<?php
namespace rtens\steps2\domain;

class Quota {
    /**
     * @var float
     */
    private $hours;
    /**
     * @var float
     */
    private $perDays;

    /**
     * @param float $hours
     * @param float $perDays
     */
    public function __construct($hours, $perDays) {
        $this->hours = $hours;
        $this->perDays = $perDays;
    }

    /**
     * @return float
     */
    public function getHours() {
        return $this->hours;
    }

    /**
     * @return float
     */
    public function getPerDays() {
        return $this->perDays;
    }

    function __toString() {
        return $this->hours . 'h / ' . $this->perDays . 'd';
    }
}