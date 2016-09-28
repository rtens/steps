<?php namespace rtens\steps\projecting;

class FinishedBlocks extends BlockList {
    /**
     * @var null|\DateTime
     */
    private $after;
    /**
     * @var null|\DateTime
     */
    private $before;

    /**
     * @param \DateTime|null $after
     * @param \DateTime|null $before
     */
    public function __construct(\DateTime $after = null, \DateTime $before = null) {
        $this->after = $after;
        $this->before = $before;
    }

    /**
     * @return Block[]
     */
    public function getBlocks() {
        return array_values(array_filter(parent::getBlocks(), function (Block $block) {
            return
                $block->getIsFinished()
                && (!$this->after || $block->getStarted() > $this->after)
                && (!$this->before || $block->getFinished() < $this->before);
        }));
    }

    public function getSpentUnits() {
        return array_sum(array_map(function (Block $block) {
            return $block->getSpentUnits();
        }, $this->getBlocks()));
    }
}