<?php namespace rtens\steps\model;

use rtens\steps\AchieveGoal;
use rtens\steps\AddGoalToPlan;
use rtens\steps\AddNote;
use rtens\steps\AddSteps;
use rtens\steps\CompleteStep;
use rtens\steps\CreateGoal;
use rtens\steps\events\BlockFinished;
use rtens\steps\events\BlockPlanned;
use rtens\steps\events\BlockStarted;
use rtens\steps\events\GoalAchieved;
use rtens\steps\events\GoalCreated;
use rtens\steps\events\NoteAdded;
use rtens\steps\events\StepAdded;
use rtens\steps\events\StepCompleted;
use rtens\steps\FinishBlock;
use rtens\steps\StartBlock;

class Steps {

    const IDENTIFIER = 'steps';

    public function handleCreateGoal(CreateGoal $c) {
        return new GoalCreated(GoalIdentifier::make([$c->getName()]), $c->getName(), Time::now());
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
                BlockIdentifier::make([$c->getGoal(), Time::now()->format('Ymd'), $count]),
                $c->getGoal(),
                $units,
                Time::now());
        }

        return $blocks;
    }

    public function handleStartBlock(StartBlock $c) {
        return new BlockStarted($c->getBlock(), Time::now());
    }

    public function handleFinishBlock(FinishBlock $c){
        return new BlockFinished($c->getBlock(), Time::now());
    }

    public function handleAddSteps(AddSteps $c){
        $steps = [];
        foreach ($c->getSteps() as $step) {
            $steps[] = new StepAdded(
                StepIdentifier::make([$c->getGoal(), $step]),
                $c->getGoal(),
                $step
            );
        }
        return $steps;
    }

    public function handleCompleteStep(CompleteStep $c) {
        return new StepCompleted($c->getStep(), Time::now());
    }

    public function handleAchieveGoal(AchieveGoal $c){
        return new GoalAchieved($c->getGoal(), Time::now());
    }

    public function handleAddNote(AddNote $c){
        return new NoteAdded($c->getGoal(), $c->getNoteContent(), Time::now());
    }
}