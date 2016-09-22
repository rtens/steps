<?php namespace rtens\steps\projecting;
use rtens\steps\events\BlockFinished;
use rtens\steps\events\BlockStarted;

class FinishedBlocks {
    /**
     * @var FinishedBlock[]
     */
    private $blocks = [];
    /**
     * @var BlockStarted|null
     */
    private $current;

    /**
     * @return float
     */
    public function getUnits() {
        return array_sum(array_map(function (FinishedBlock $block) {
            return $block->getUnits();
        }, $this->blocks));
    }

    /**
     * @return FinishedBlock[]
     */
    public function getBlocks() {
        return $this->blocks;
    }

    public function applyBlockStarted(BlockStarted $e) {
        $this->current = $e;
    }

    public function applyBlockFinished(BlockFinished $e) {
        $this->blocks[] = new FinishedBlock($e->getBlock(), $this->current->getWhen(), $e->getWhen());
    }
}