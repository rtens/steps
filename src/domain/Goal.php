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
    /**
     * @var null|GoalIdentifier
     */
    private $parent;

    /**
     * @return GoalIdentifier|\rtens\udity\AggregateIdentifier
     */
    public function getIdentifier() {
        return parent::getIdentifier();
    }

    /**
     * @return null|GoalIdentifier
     */
    public function getParent() {
        return $this->parent;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isOpen() {
        return !$this->isAchieved() && !$this->isGivenUp();
    }

    /**
     * @return bool
     */
    public function isAchieved() {
        return $this->achieved;
    }

    /**
     * @return bool
     */
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

    public function created($name) {
        $this->name = $name;
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

    public function doMove(GoalIdentifier $parent = null) {
        if ($parent == $this->getIdentifier()) {
            throw new \Exception('Goal cannot be its own parent');
        }
    }

    public function didMove(GoalIdentifier $parent = null) {
        $this->parent = $parent;
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