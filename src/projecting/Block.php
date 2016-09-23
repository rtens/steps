<?php namespace rtens\steps\projecting;

use rtens\steps\events\BlockFinished;
use rtens\steps\events\BlockPlanned;
use rtens\steps\events\BlockStarted;
use rtens\steps\events\GoalCreated;
use rtens\steps\events\StepAdded;
use rtens\steps\events\StepCompleted;
use rtens\steps\model\BlockIdentifier;
use rtens\steps\model\GoalIdentifier;
use rtens\steps\model\Steps;
use rtens\steps\model\Time;

class Block {
    /**
     * @var BlockIdentifier
     */
    private $block;
    /**
     * @var float
     */
    private $units;
    /**
     * @var Goal
     */
    private $goal;
    /**
     * @var \DateTime
     */
    private $planned;
    /**
     * @var null|\DateTime
     */
    private $started;
    /**
     * @var null|\DateTime
     */
    private $finished;

    /**
     * @param BlockIdentifier $block
     * @param Goal $goal
     */
    public function __construct(BlockIdentifier $block, Goal $goal) {
        $this->block = $block;
        $this->goal = $goal;
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
    public function getGoalIdentifier() {
        return $this->goal->getGoal();
    }

    /**
     * @return float
     */
    public function getUnits() {
        return $this->units;
    }

    /**
     * @return string
     */
    public function getGoalName() {
        return $this->goal->getName();
    }

    /**
     * @return null|string
     */
    public function getNextStep() {
        return $this->goal->getNextStep();
    }

    /**
     * @return \DateTime|null
     */
    public function getStarted() {
        return $this->started;
    }

    /**
     * @return \DateTime|null
     */
    public function getFinished() {
        return $this->finished;
    }

    /**
     * @return bool
     */
    public function getIsFinished() {
        return !!$this->finished;
    }

    public function getSpentUnits() {
        return ($this->finished->getTimestamp() - $this->started->getTimestamp()) / Steps::UNIT_SECONDS;
    }

    /**
     * @return bool
     */
    public function wasPlannedToday() {
        return $this->planned->setTime(0, 0) == Time::at('today');
    }

    public function applyBlockPlanned(BlockPlanned $e) {
        if ($this->block != $e->getBlock()) {
            return;
        }
        $this->units = $e->getUnits();
        $this->planned = $e->getWhen();
    }

    public function applyBlockFinished(BlockFinished $e) {
        if ($this->block != $e->getBlock()) {
            return;
        }
        $this->finished = $e->getWhen();
    }

    public function applyBlockStarted(BlockStarted $e) {
        if ($this->block != $e->getBlock()) {
            return;
        }
        $this->started = $e->getWhen();
    }

    public function applyGoalCreated(GoalCreated $e) {
        $this->applyGoal(__FUNCTION__, $e);
    }

    public function applyStepAdded(StepAdded $e) {
        $this->applyGoal(__FUNCTION__, $e);
    }

    public function applyStepCompleted(StepCompleted $e) {
        $this->applyGoal(__FUNCTION__, $e);
    }

    private function applyGoal($function, $e) {
        call_user_func([$this->goal, $function], $e);
    }
}