<?php namespace rtens\steps\projecting;

use rtens\steps\events\PlanSorted;
use rtens\steps\model\BlockIdentifier;

class Plan extends BlockList {
    /**
     * @var BlockIdentifier[]
     */
    private $sorted = [];

    /**
     * @return Block[]
     */
    public function getBlocks() {
        $filtered = array_values(array_filter(parent::getBlocks(), function (Block $block) {
            return !$block->isCancelled() && !$block->getIsFinished() && $block->wasPlannedToday();
        }));
        $original = array_values(array_map(function (Block $block) {
            return $block->getBlock();
        }, $filtered));

        if ($this->sorted) {
            usort($filtered, function (Block $a, Block $b) use ($original) {
                if (in_array($a->getBlock(), $this->sorted) && !in_array($b->getBlock(), $this->sorted)) {
                    return -1;
                } else if (!in_array($a->getBlock(), $this->sorted) && in_array($b->getBlock(), $this->sorted)) {
                    return 1;
                } else if (in_array($a->getBlock(), $this->sorted) && in_array($b->getBlock(), $this->sorted)) {
                    return array_search($a->getBlock(), $this->sorted) - array_search($b->getBlock(), $this->sorted);
                } else {
                    return array_search($a->getBlock(), $original) - array_search($b->getBlock(), $original);
                }
            });
        }

        return array_values($filtered);
    }

    public function applyPlanSorted(PlanSorted $e) {
        $this->sorted = $e->getBlocks();
    }
}