<?php namespace rtens\steps\projecting;

class Plan extends BlockList {

    /**
     * @return Block[]
     */
    public function getBlocks() {
        return array_values(array_filter(parent::getBlocks(), function (Block $block) {
            return !$block->isCancelled() && !$block->getIsFinished() && $block->wasPlannedToday();
        }));
    }
}