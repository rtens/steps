<?php namespace rtens\steps\model;
use rtens\steps\events\BlockFinished;
use rtens\steps\events\BlockPlanned;

class Plan {

    /**
     * @var Block[]
     */
    private $blocks = [];

    /**
     * @return Block[]
     */
    public function getBlocks() {
        return array_values($this->blocks);
    }

    public function applyBlockPlanned(BlockPlanned $e) {
        $this->blocks[(string)$e->getBlock()] = new Block(
            $e->getBlock(),
            $e->getGoal(),
            $e->getUnits()
        );
    }

    public function applyBlockFinished(BlockFinished $e) {
        unset($this->blocks[(string)$e->getBlock()]);
    }
}