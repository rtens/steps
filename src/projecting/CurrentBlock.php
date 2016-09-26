<?php namespace rtens\steps\projecting;

class CurrentBlock extends Plan {

    /**
     * @return Block[]
     */
    public function getBlocks() {
        return array_values(array_filter(parent::getBlocks(), function (Block $block) {
            return $block->getStarted() && !$block->getIsFinished();
        }));
    }

    public function getNextBlock() {
        if (!parent::getBlocks()) {
            return null;
        }
        return parent::getBlocks()[0];
    }

    public function getUnitsLeft() {
        return array_sum(array_map(function (Block $block) {
            return $block->getUnits();
        }, parent::getBlocks()));
    }
}