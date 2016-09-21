<?php namespace rtens\steps;
use rtens\steps\model\GoalIdentifier;
use rtens\steps\model\Steps;
use watoki\karma\implementations\commandQuery\Command;

class AddSteps implements Command {
    /**
     * @var GoalIdentifier
     */
    private $goal;
    /**
     * @var string[]
     */
    private $steps;

    /**
     * @param GoalIdentifier $goal
     * @param string[] $steps
     */
    public function __construct(GoalIdentifier $goal, array $steps) {
        $this->goal = $goal;
        $this->steps = $steps;
    }

    /**
     * @return GoalIdentifier
     */
    public function getGoal() {
        return $this->goal;
    }

    /**
     * @return string[]
     */
    public function getSteps() {
        return $this->steps;
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