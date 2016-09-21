<?php namespace rtens\steps;
use rtens\steps\model\GoalIdentifier;
use rtens\steps\model\Steps;
use watoki\karma\implementations\commandQuery\Command;

class SetDeadline implements Command {
    /**
     * @var GoalIdentifier
     */
    private $goal;
    /**
     * @var \DateTime
     */
    private $deadline;

    /**
     * @param GoalIdentifier $goal
     * @param \DateTime $deadline
     */
    public function __construct(GoalIdentifier $goal, \DateTime $deadline) {
        $this->goal = $goal;
        $this->deadline = $deadline;
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
     * @return mixed
     */
    public function getAggregateIdentifier() {
        return Steps::IDENTIFIER;
    }

    /**
     * @return object
     */
    public function getAggregateRoot() {
        return new Steps();
    }
}