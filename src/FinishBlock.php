<?php namespace rtens\steps;
use rtens\steps\model\BlockIdentifier;
use rtens\steps\model\Steps;
use rtens\steps\model\Time;
use watoki\karma\implementations\commandQuery\Command;

class FinishBlock implements Command {
    /**
     * @var BlockIdentifier
     */
    private $block;
    /**
     * @var \DateTime
     */
    private $when;
    /**
     * @var bool
     */
    private $goalAchieved;

    /**
     * @param BlockIdentifier $block
     * @param bool $goalAchieved
     * @param \DateTime|null $when
     */
    public function __construct(BlockIdentifier $block, $goalAchieved = false, \DateTime $when = null) {
        $this->block = $block;
        $this->goalAchieved = $goalAchieved;
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

    /**
     * @return boolean
     */
    public function isGoalAchieved() {
        return $this->goalAchieved;
    }
}