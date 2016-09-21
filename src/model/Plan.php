<?php namespace rtens\steps\model;
use rtens\steps\events\BlockPlanned;

class Plan {

    private $blocks = [];

    public function getBlocks() {
        return $this->blocks;
    }

    public function applyBlockPlanned(BlockPlanned $e) {
        $this->blocks[] = new Block(
            $e->getBlock(),
            $e->getGoal(),
            $e->getUnits()
        );
    }
}