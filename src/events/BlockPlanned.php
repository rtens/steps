<?php namespace rtens\steps\events;
use rtens\steps\model\BlockIdentifier;
use rtens\steps\model\GoalIdentifier;

class BlockPlanned {
    /**
     * @var BlockIdentifier
     */
    private $block;
    /**
     * @var GoalIdentifier
     */
    private $goal;
    /**
     * @var float
     */
    private $units;
    /**
     * @var \DateTime
     */
    private $when;

    /**
     * @param BlockIdentifier $block
     * @param GoalIdentifier $goal
     * @param float $units
     * @param \DateTime $when
     */
    public function __construct(BlockIdentifier $block, GoalIdentifier $goal, $units, \DateTime $when) {
        $this->block = $block;
        $this->goal = $goal;
        $this->units = $units;
        $this->when = $when;
    }

    /**
     * @return BlockIdentifier
     */
    public function getBlock() {
        return $this->block;
    }

    /**
     * @return GoalIdentifier
     */
    public function getGoal() {
        return $this->goal;
    }

    /**
     * @return float
     */
    public function getUnits() {
        return $this->units;
    }

    /**
     * @return \DateTime
     */
    public function getWhen() {
        return $this->when;
    }
}