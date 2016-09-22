<?php namespace rtens\steps\projecting;
use rtens\steps\events\BlockStarted;
use rtens\steps\model\BlockIdentifier;

class CurrentBlock {
    /**
     * @var null|BlockIdentifier
     */
    private $block;
    /**
     * @var null|\DateTime
     */
    private $started;

    /**
     * @return null|BlockIdentifier
     */
    public function getBlock() {
        return $this->block;
    }

    /**
     * @return \DateTime|null
     */
    public function getStarted() {
        return $this->started;
    }

    public function applyBlockStarted(BlockStarted $e) {
        $this->block = $e->getBlock();
        $this->started = $e->getWhen();
    }

    public function applyBlockFinished() {
        $this->block = null;
        $this->started = null;
    }
}