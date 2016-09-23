<?php namespace rtens\steps;
use rtens\steps\model\BlockIdentifier;
use rtens\steps\model\Steps;
use watoki\karma\implementations\commandQuery\Command;

class CancelBlock implements Command {
    /**
     * @var BlockIdentifier
     */
    private $block;

    /**
     * @param BlockIdentifier $block
     */
    public function __construct(BlockIdentifier $block) {
        $this->block = $block;
    }

    /**
     * @return BlockIdentifier
     */
    public function getBlock() {
        return $this->block;
    }

    /**
     * @return mixed
     */
    public function getAggregateIdentifier() {
        return Steps::IDENTIFIER;
    }

    /**
     * @return object
     */
    public function getAggregateRoot() {
        return new Steps();
    }
}