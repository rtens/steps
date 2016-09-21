<?php namespace rtens\steps\model;
class Block {
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
     * @param BlockIdentifier $block
     * @param GoalIdentifier $goal
     * @param float $units
     */
    public function __construct(BlockIdentifier $block, GoalIdentifier $goal, $units) {
        $this->block = $block;
        $this->goal = $goal;
        $this->units = $units;
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
}