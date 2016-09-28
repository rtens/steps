<?php namespace rtens\steps\model;

use rtens\steps\AchieveGoal;
use rtens\steps\AddNote;
use rtens\steps\AddSteps;
use rtens\steps\CancelBlock;
use rtens\steps\CompleteStep;
use rtens\steps\CreateGoal;
use rtens\steps\events\BlockCancelled;
use rtens\steps\events\BlockFinished;
use rtens\steps\events\BlockPlanned;
use rtens\steps\events\BlockStarted;
use rtens\steps\events\DeadlineSet;
use rtens\steps\events\GoalAchieved;
use rtens\steps\events\GoalCreated;
use rtens\steps\events\GoalRated;
use rtens\steps\events\NoteAdded;
use rtens\steps\events\PlanSorted;
use rtens\steps\events\StepAdded;
use rtens\steps\events\StepCompleted;
use rtens\steps\events\StepsSorted;
use rtens\steps\FinishBlock;
use rtens\steps\PlanBlock;
use rtens\steps\RateGoal;
use rtens\steps\SetDeadline;
use rtens\steps\SortPlan;
use rtens\steps\SortSteps;
use rtens\steps\StartBlock;

class Steps {

    const IDENTIFIER = 'steps';
    const UNIT_SECONDS = 25 * 60;
    const MAX_URGENCY = 10;
    const MAX_IMPORTANCE = 10;
    const DEADLINE_ZONE_SECONDS = 30 * 24 * 60 * 60;

    /**
     * @var null|BlockIdentifier
     */
    private $currentBlock;
    /**
     * @var BlockIdentifier[]
     */
    private $finishedBlocks = [];
    /**
     * @var BlockIdentifier[]
     */
    private $startedBlocks = [];
    /**
     * @var GoalIdentifier[] indexed by BlockIdentifier
     */
    private $goalOfBlocks = [];

    public function handleCreateGoal(CreateGoal $c) {
        return new GoalCreated(GoalIdentifier::make([$c->getName()]), $c->getName(), Time::now());
    }

    public function handlePlanBlock(PlanBlock $c) {
        $blocks = [];

        $unitsLeft = $c->getUnits();
        $count = 0;
        while ($unitsLeft > 0) {
            $units = $unitsLeft;
            if ($c->isSplitIntoUnits() && $unitsLeft > 1) {
                $units = 1;
            }
            $unitsLeft -= $units;
            $count++;

            $identifier = BlockIdentifier::make([$c->getGoal(), Time::now()->format('Ymd'), $count]);
            $blocks[] = new BlockPlanned(
                $identifier,
                $c->getGoal(),
                $units,
                Time::now());
         }

        return $blocks;
    }

    public function applyBlockPlanned(BlockPlanned $e){
        $this->goalOfBlocks[(string)$e->getBlock()] = $e->getGoal();

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
        $this->startedBlocks[] = $e->getBlock();
    }

    public function handleFinishBlock(FinishBlock $c) {
        if (!$this->currentBlock) {
            throw new \Exception('No block was started.');
        }
        if ($this->currentBlock != $c->getBlock()) {
            throw new \Exception('This is not the current block.');
        }

        $events = [new BlockFinished($c->getBlock(), $c->getWhen())];

        if ($c->isGoalAchieved()) {
            $events[] = new GoalAchieved($this->goalOfBlocks[(string)$c->getBlock()], $c->getWhen());
        }

        return $events;
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

    public function handleCancelBlock(CancelBlock $c) {
        if (in_array($c->getBlock(), $this->startedBlocks)) {
            throw new \Exception('Cannot cancel a block after it has been started.');
        }
        return new BlockCancelled($c->getBlock(), Time::now());
    }

    public function handleSortPlan(SortPlan $c) {
        return new PlanSorted($c->getBlocks(), Time::now());
    }

    public function handleSortSteps(SortSteps $c) {
        return new StepsSorted($c->getGoal(), $c->getSteps(), Time::now());
    }
}