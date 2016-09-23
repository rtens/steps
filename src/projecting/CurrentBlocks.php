<?php namespace rtens\steps\projecting;

class CurrentBlocks extends BlockList {

    /**
     * @return Block[]
     */
    public function getBlocks() {
        return array_values(array_filter(parent::getBlocks(), function (Block $block) {
            return $block->getStarted() && !$block->getIsFinished();
        }));
    }
}