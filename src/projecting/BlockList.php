<?php namespace rtens\steps\projecting;
use rtens\steps\events\BlockCancelled;
use rtens\steps\events\BlockFinished;
use rtens\steps\events\BlockPlanned;
use rtens\steps\events\BlockStarted;
use rtens\steps\events\GoalCreated;
use rtens\steps\events\StepAdded;
use rtens\steps\events\StepCompleted;

class BlockList {
    /**
     * @var Block[]
     */
    private $blocks = [];
    /**
     * @var Goal[]
     */
    private $goals = [];

    /**
     * @return float
     */
    public function getUnits() {
        return array_sum(array_map(function (Block $block) {
            return $block->getUnits();
        }, $this->getBlocks()));
    }

    /**
     * @return Block[]
     */
    public function getBlocks() {
        return array_values($this->blocks);
    }

    public function applyBlockPlanned(BlockPlanned $e) {
        $this->blocks[] = new Block($e->getBlock(), $this->goals[(string)$e->getGoal()]);
        $this->apply($this->blocks, __FUNCTION__, $e);
    }

    public function applyBlockFinished(BlockFinished $e) {
        $this->apply($this->blocks, __FUNCTION__, $e);
    }

    public function applyBlockStarted(BlockStarted $e) {
        $this->apply($this->blocks, __FUNCTION__, $e);
    }

    public function applyBlockCancelled(BlockCancelled $e) {
        $this->apply($this->blocks, __FUNCTION__, $e);
    }

    private function apply($to, $function, $event) {
        foreach ($to as $target) {
            call_user_func([$target, $function], $event);
        }
    }

    public function applyGoalCreated(GoalCreated $e) {
        $this->goals[(string)$e->getGoal()] = new Goal($e->getGoal());
        $this->apply($this->goals, __FUNCTION__, $e);
    }

    public function applyStepAdded(StepAdded $e) {
        $this->apply($this->goals, __FUNCTION__, $e);
    }

    public function applyStepCompleted(StepCompleted $e) {
        $this->apply($this->goals, __FUNCTION__, $e);
    }
}