<?php namespace rtens\steps\events;
use rtens\steps\model\GoalIdentifier;

class GoalAchieved {
    /**
     * @var GoalIdentifier
     */
    private $goal;
    /**
     * @var \DateTime
     */
    private $when;

    /**
     * @param GoalIdentifier $goal
     * @param \DateTime $when
     */
    public function __construct(GoalIdentifier $goal, \DateTime $when) {
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
     * @return \DateTime
     */
    public function getWhen() {
        return $this->when;
    }
}