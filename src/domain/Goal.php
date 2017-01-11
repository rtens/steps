<?php
namespace rtens\steps2\domain;

use rtens\udity\domain\objects\DomainObject;

class Goal extends DomainObject {
    /**
     * @var string
     */
    private $name;
    /**
     *
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

    public function isOpen() {
        return !$this->isAchieved() && !$this->isGivenUp();
    }

    public function isAchieved() {
        return $this->achieved;
    }

    public function isGivenUp() {
        return $this->givenUp;
    }

    public function caption() {
        $caption = $this->getName();
        if ($this->isAchieved()) {
            $caption .= ' (achieved)';
        } else if ($this->isGivenUp()) {
            $caption .= ' (given up)';
        }
        return $caption;
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