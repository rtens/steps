<?php namespace rtens\steps\model;

use rtens\steps\CreateGoal;
use rtens\steps\events\GoalCreated;

class Steps {

    const IDENTIFIER = 'steps';

    public function handleCreateGoal(CreateGoal $c) {
        return new GoalCreated($c->getName());
    }
}