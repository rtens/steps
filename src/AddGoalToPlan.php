<?php namespace rtens\steps;
use rtens\steps\model\GoalIdentifier;
use rtens\steps\model\Steps;
use watoki\karma\implementations\commandQuery\Command;

class AddGoalToPlan implements Command {

    /**
     * @var GoalIdentifier
     */
    private $goal;
    /**
     * @var float
     */
    private $units;

    /**
     * @param GoalIdentifier $goal
     * @param float $units
     */
    public function __construct(GoalIdentifier $goal, $units = 1.0) {
        $this->goal = $goal;
        $this->units = $units;
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