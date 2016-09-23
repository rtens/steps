<?php namespace rtens\steps\projecting;
use rtens\steps\model\BlockIdentifier;

class Block {
    /**
     * @var float
     */
    private $units;

    /**
     * @param BlockIdentifier $block
     * @param float $units
     */
    public function __construct(BlockIdentifier $block, $units) {
        $this->block = $block;
        $this->units = $units;
    }

    /**
     * @return BlockIdentifier
     */
    public function getBlock() {
        return $this->block;
    }

    /**
     * @return float
     */
    public function getUnits() {
        return $this->units;
    }
}