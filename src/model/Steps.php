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
use rtens\steps\events\DeadlineSet;
use rtens\steps\events\GoalAchieved;
use rtens\steps\events\GoalCreated;
use rtens\steps\events\GoalRated;
use rtens\steps\events\NoteAdded;
use rtens\steps\events\StepAdded;
use rtens\steps\events\StepCompleted;
use rtens\steps\FinishBlock;
use rtens\steps\RateGoal;
use rtens\steps\SetDeadline;
use rtens\steps\StartBlock;

class Steps {

    const IDENTIFIER = 'steps';
    const UNIT_SECONDS = 25 * 60;

    /**
     * @var null|BlockIdentifier
     */
    private $currentBlock;
    /**
     * @var BlockIdentifier[]
     */
    private $finishedBlocks = [];

    public function handleCreateGoal(CreateGoal $c) {
        return new GoalCreated(GoalIdentifier::make([$c->getName()]), $c->getName(), Time::now());
    }

    public function handleAddGoalToPlan(AddGoalToPlan $c) {
        $blocks = [];

        $unitsLeft = $c->getUnits();
        $count = 0;
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
        if ($this->currentBlock) {
            throw new \Exception('A block has already been started.');
        }
        if (in_array($c->getBlock(), $this->finishedBlocks)) {
            throw new \Exception('This block is already finished.');
        }

        return new BlockStarted($c->getBlock(), $c->getWhen());
    }

    public function applyBlockStarted(BlockStarted $e) {
        $this->currentBlock = $e->getBlock();
    }

    public function handleFinishBlock(FinishBlock $c) {
        if (!$this->currentBlock) {
            throw new \Exception('No block was started.');
        }
        if ($this->currentBlock != $c->getBlock()) {
            throw new \Exception('This is not the current block.');
        }

        return new BlockFinished($c->getBlock(), $c->getWhen());
    }

    public function applyBlockFinished(BlockFinished $e) {
        $this->currentBlock = null;
        $this->finishedBlocks[] = $e->getBlock();
    }

    public function handleAddSteps(AddSteps $c) {
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

    public function handleAchieveGoal(AchieveGoal $c) {
        return new GoalAchieved($c->getGoal(), $c->getWhen());
    }

    public function handleAddNote(AddNote $c) {
        return new NoteAdded($c->getGoal(), $c->getNoteContent(), Time::now());
    }

    public function handleRateGoal(RateGoal $c) {
        return new GoalRated($c->getGoal(), $c->getImportance(), $c->getUrgency(), Time::now());
    }

    public function handleSetDeadline(SetDeadline $c) {
        return new DeadlineSet($c->getGoal(), $c->getDeadline(), Time::now());
    }
}