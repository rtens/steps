<?php namespace rtens\steps\projecting;

use rtens\steps\events\GoalCreated;
use rtens\steps\events\StepAdded;
use rtens\steps\model\Goal;

class GoalList {
    /**
     * @var Goal[]
     */
    private $goals = [];

    /**
     * @return Goal[]
     */
    public function getGoals() {
        return array_values($this->goals);
    }

    public function applyGoalCreated(GoalCreated $e) {
        $this->goals[(string)$e->getGoal()] = new Goal($e->getGoal(), $e->getName());
    }

    public function applyStepAdded(StepAdded $e) {
        $this->goals[(string)$e->getGoal()]->addStep($e->getStep());
    }
}