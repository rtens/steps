<?php namespace rtens\steps\events;
use rtens\steps\model\BlockIdentifier;

class BlockStarted {
    /**
     * @var BlockIdentifier
     */
    private $block;
    /**
     * @var \DateTime
     */
    private $when;

    /**
     * @param BlockIdentifier $block
     * @param \DateTime $when
     */
    public function __construct(BlockIdentifier $block, \DateTime $when) {
        $this->block = $block;
        $this->when = $when;
    }

    /**
     * @return BlockIdentifier
     */
    public function getBlock() {
        return $this->block;
    }

    /**
     * @return \DateTime
     */
    public function getWhen() {
        return $this->when;
    }
}