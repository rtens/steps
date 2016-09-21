<?php namespace rtens\steps\events;

use rtens\steps\model\GoalIdentifier;

class GoalCreated {
    /**
     * @var GoalIdentifier
     */
    private $goal;
    /**
     * @var string
     */
    private $name;
    /**
     * @var \DateTime
     */
    private $when;

    /**
     * @param GoalIdentifier $goal
     * @param string $name
     * @param \DateTime $when
     */
    public function __construct(GoalIdentifier $goal, $name, \DateTime $when) {
        $this->name = $name;
        $this->goal = $goal;
        $this->when = $when;
    }

    /**
     * @return GoalIdentifier
     */
    public function getGoal() {
        return $this->goal;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return \DateTime
     */
    public function getWhen() {
        return $this->when;
    }
}