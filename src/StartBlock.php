<?php namespace rtens\steps;
use rtens\steps\model\BlockIdentifier;
use rtens\steps\model\Steps;
use rtens\steps\model\Time;
use watoki\karma\implementations\commandQuery\Command;

class StartBlock implements Command {
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
     * @param \DateTime|null $when
     */
    public function __construct(BlockIdentifier $block, \DateTime $when = null) {
        $this->block = $block;
        $this->when = $when ?: Time::now();
    }

    /**
     * @return BlockIdentifier
     */
    public function getBlock() {
        return $this->block;
    }

    /**
     * @return \DateTime|null
     */
    public function getWhen() {
        return $this->when;
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