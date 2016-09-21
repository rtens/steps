<?php namespace rtens\steps\model;

use rtens\steps\CreateGoal;
use rtens\steps\events\GoalCreated;
use rtens\steps\events\WorkPlanned;
use rtens\steps\PlanWork;

class Steps {

    const IDENTIFIER = 'steps';

    public function handleCreateGoal(CreateGoal $c) {
        return new GoalCreated(GoalIdentifier::make($c->getName()), $c->getName(), Time::now());
    }

    public function handlePlanWork(PlanWork $c) {
        return new WorkPlanned($c->getGoal(), $c->getUnits(), Time::now());
    }
}