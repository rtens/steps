<?php
namespace rtens\steps2\domain;

use rtens\udity\domain\objects\DomainObject;

class Goal extends DomainObject {
    /**
     * @var string
     */
    private $name;
    /**
     * @var bool
     */
    private $achieved = false;
    /**
     * @var bool
     */
    private $givenUp = false;

    public function created($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function caption() {
        return $this->getName();
    }

    public function doAchieve() {
        $this->guardStillOpen();
    }

    public function didAchieve() {
        $this->achieved = true;
    }

    public function doGiveUp() {
        $this->guardStillOpen();
    }

    public function didGiveUp() {
        $this->givenUp = true;
    }

    private function guardStillOpen() {
        if ($this->achieved) {
            throw new \Exception('Goal is already achieved');
        }
        if ($this->givenUp) {
            throw new \Exception('Goal was already given up');
        }
    }
}