<?php namespace rtens\steps\projecting;
use rtens\steps\events\BlockFinished;
use rtens\steps\events\BlockPlanned;
use rtens\steps\model\Time;

class Plan {
    /**
     * @var Block[]
     */
    private $blocks = [];

    /**
     * @return float
     */
    public function getUnits() {
        return array_sum(array_map(function (Block $block) {
            return $block->getUnits();
        }, $this->blocks));
    }

    /**
     * @return Block[]
     */
    public function getBlocks() {
        return array_values($this->blocks);
    }

    public function applyBlockPlanned(BlockPlanned $e) {
        if (!$this->wasPlannedToday($e)) {
            return;
        }

        $this->blocks[(string)$e->getBlock()] = new Block(
            $e->getBlock(),
            $e->getGoal(),
            $e->getUnits()
        );
    }

    public function applyBlockFinished(BlockFinished $e) {
        unset($this->blocks[(string)$e->getBlock()]);
    }

    private function wasPlannedToday(BlockPlanned $e) {
        return $e->getWhen()->setTime(0, 0) == Time::at('today');
    }
}