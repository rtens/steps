<?php namespace rtens\steps\events;
use rtens\steps\model\BlockIdentifier;

class PlanSorted {
    /**
     * @var array|BlockIdentifier[]
     */
    private $blocks;
    /**
     * @var \DateTime
     */
    private $when;

    /**
     * @param BlockIdentifier[] $blocks
     * @param \DateTime $when
     */
    public function __construct(array $blocks, \DateTime $when) {
        $this->blocks = $blocks;
        $this->when = $when;
    }

    /**
     * @return array|BlockIdentifier[]
     */
    public function getBlocks() {
        return $this->blocks;
    }

    /**
     * @return \DateTime
     */
    public function getWhen() {
        return $this->when;
    }
}