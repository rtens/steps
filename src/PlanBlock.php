<?php namespace rtens\steps;
use rtens\steps\model\GoalIdentifier;
use rtens\steps\model\Steps;
use watoki\karma\implementations\commandQuery\Command;

class PlanBlock implements Command {

    /**
     * @var GoalIdentifier
     */
    private $goal;
    /**
     * @var float
     */
    private $units;
    /**
     * @var bool
     */
    private $splitIntoUnits;

    /**
     * @param GoalIdentifier $goal
     * @param float $units
     * @param bool $splitIntoUnits
     */
    public function __construct(GoalIdentifier $goal, $units = 1.0, $splitIntoUnits = true) {
        $this->goal = $goal;
        $this->units = $units;
        $this->splitIntoUnits = $splitIntoUnits;
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
     * @return boolean
     */
    public function isSplitIntoUnits() {
        return $this->splitIntoUnits;
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