<?php namespace rtens\steps;
use rtens\steps\model\GoalIdentifier;
use rtens\steps\model\Steps;
use watoki\karma\implementations\commandQuery\Command;

class RateGoal implements Command {
    /**
     * @var GoalIdentifier
     */
    private $goal;
    /**
     * @var float
     */
    private $importance;
    /**
     * @var float
     */
    private $urgency;

    /**
     * @param GoalIdentifier $goal
     * @param float $importance
     * @param float $urgency
     */
    public function __construct(GoalIdentifier $goal, $importance, $urgency) {
        $this->goal = $goal;
        $this->importance = $importance;
        $this->urgency = $urgency;
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
    public function getImportance() {
        return $this->importance;
    }

    /**
     * @return float
     */
    public function getUrgency() {
        return $this->urgency;
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