<?php namespace rtens\steps\projecting;

use rtens\steps\events\GoalCreated;
use rtens\steps\model\Goal;

class GoalList {

    private $goals = [];

    public function getGoals() {
        return $this->goals;
    }

    public function applyGoalCreated(GoalCreated $e) {
        $this->goals[] = new Goal($e->getGoal(), $e->getName());
    }
}