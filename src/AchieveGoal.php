<?php namespace rtens\steps;
use rtens\steps\model\GoalIdentifier;
use rtens\steps\model\Steps;
use watoki\karma\implementations\commandQuery\Command;

class AchieveGoal implements Command {
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
     * @return GoalIdentifier
     */
    public function getGoal() {
        return $this->goal;
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