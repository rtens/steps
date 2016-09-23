<?php namespace rtens\steps;
use rtens\steps\model\GoalIdentifier;
use rtens\steps\projecting\Goal;
use watoki\karma\implementations\commandQuery\Query;

class ShowGoal implements Query {
    /**
     * @var GoalIdentifier
     */
    private $goal;

    /**
     * @param GoalIdentifier $goal
     */
    public function __construct(GoalIdentifier $goal) {
        $this->goal = $goal;
    }

    /**
     * @return object
     */
    public function getProjection() {
        return new Goal($this->goal, '');
    }
}