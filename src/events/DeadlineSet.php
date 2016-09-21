<?php namespace rtens\steps\events;
use rtens\steps\model\GoalIdentifier;

class DeadlineSet {
    /**
     * @var GoalIdentifier
     */
    private $goal;
    /**
     * @var \DateTime
     */
    private $deadline;
    /**
     * @var \DateTime
     */
    private $when;

    /**
     * @param GoalIdentifier $goal
     * @param \DateTime $deadline
     * @param \DateTime $when
     */
    public function __construct(GoalIdentifier $goal, \DateTime $deadline, \DateTime $when) {
        $this->goal = $goal;
        $this->deadline = $deadline;
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
    public function getDeadline() {
        return $this->deadline;
    }

    /**
     * @return \DateTime
     */
    public function getWhen() {
        return $this->when;
    }
}