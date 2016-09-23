<?php namespace rtens\steps\projecting;

class FinishedBlocks extends BlockList {

    /**
     * @return Block[]
     */
    public function getBlocks() {
        return array_values(array_filter(parent::getBlocks(), function (Block $block) {
            return $block->getIsFinished();
        }));
    }

    public function getSpentUnits() {
        return array_sum(array_map(function (Block $block) {
            return $block->getSpentUnits();
        }, $this->getBlocks()));
    }
}