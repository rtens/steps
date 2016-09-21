<?php namespace rtens\steps\model;

use rtens\steps\AddGoalToPlan;
use rtens\steps\CreateGoal;
use rtens\steps\events\BlockPlanned;
use rtens\steps\events\GoalCreated;

class Steps {

    const IDENTIFIER = 'steps';

    public function handleCreateGoal(CreateGoal $c) {
        return new GoalCreated(GoalIdentifier::make($c->getName()), $c->getName(), Time::now());
    }

    public function handleAddGoalToPlan(AddGoalToPlan $c) {
        $blocks = [];

        $unitsLeft = $c->getUnits();
        $count = 0
        ;
        while ($unitsLeft > 0) {
            $units = $unitsLeft > 1 ? 1 : $unitsLeft;
            $unitsLeft -= $units;
            $count++;

            $blocks[] = new BlockPlanned(
                BlockIdentifier::make($c->getGoal() . Time::now()->format('Ymd') . $count),
                $c->getGoal(),
                $units,
                Time::now());
        }

        return $blocks;
    }
}