<?php namespace rtens\steps\projecting;
use rtens\steps\model\BlockIdentifier;
use rtens\steps\model\Steps;

class FinishedBlock {
    /**
     * @var BlockIdentifier
     */
    private $block;
    /**
     * @var \DateTime
     */
    private $started;
    /**
     * @var \DateTime
     */
    private $finished;

    /**
     * @param BlockIdentifier $block
     * @param \DateTime $started
     * @param \DateTime $finished
     */
    public function __construct(BlockIdentifier $block, \DateTime $started, \DateTime $finished) {
        $this->block = $block;
        $this->started = $started;
        $this->finished = $finished;
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
    public function getStarted() {
        return $this->started;
    }

    /**
     * @return \DateTime
     */
    public function getFinished() {
        return $this->finished;
    }

    public function getUnits() {
        return ($this->finished->getTimestamp() - $this->started->getTimestamp()) / Steps::UNIT_SECONDS;
    }
}