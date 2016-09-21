<?php namespace rtens\steps\events;
use rtens\steps\model\GoalIdentifier;

class WorkPlanned {
    /**
     * @var GoalIdentifier
     */
    private $goal;
    /**
     * @var float
     */
    private $units;
    /**
     * @var \DateTime
     */
    private $when;

    /**
     * @param GoalIdentifier $goal
     * @param float $units
     * @param \DateTime $when
     */
    public function __construct(GoalIdentifier $goal, $units, \DateTime $when) {
        $this->goal = $goal;
        $this->units = $units;
        $this->when = $when;
    }

    /**
     * @return GoalIdentifier
     */
    public function getGoal() {
        return $this->goal;
    }

    /**
     * @return float
     */
    public function getUnits() {
        return $this->units;
    }

    /**
     * @return \DateTime
     */
    public function getWhen() {
        return $this->when;
    }
}