<?php
namespace rtens\steps2\domain;

use rtens\udity\utils\Time;

class Step {
    public static $UNIT_SECS = 1500;
    /**
     * @var GoalIdentifier
     */
    private $goal;
    /**
     * @var float
     */
    private $units;
    /**
     * @var null|\DateTimeImmutable
     */
    private $started;
    /**
     * @var null|\DateTimeImmutable
     */
    private $completed;
    /**
     * @var bool
     */
    private $skipped = false;

    /**
     * @param GoalIdentifier $goal
     * @param float $units
     */
    public function __construct(GoalIdentifier $goal, $units) {
        $this->goal = $goal;
        $this->units = $units;
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
     * @return bool
     */
    public function isBeingTaken() {
        return $this->started && !$this->completed;
    }

    public function getUnitsLeft() {
        if ($this->completed) {
            return 0;
        }
        if (!$this->started) {
            return $this->units;
        }

        $totalSeconds = $this->units * self::$UNIT_SECS;
        $secondsPassed = Time::now()->getTimestamp() - $this->started->getTimestamp();
        return ($totalSeconds - $secondsPassed) / self::$UNIT_SECS;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getStarted() {
        return $this->started;
    }

    /**
     * @param \DateTimeImmutable $started
     */
    public function setStarted(\DateTimeImmutable $started) {
        $this->started = $started;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getCompleted() {
        return $this->completed;
    }

    /**
     * @param \DateTimeImmutable $completed
     */
    public function setCompleted(\DateTimeImmutable $completed) {
        $this->completed = $completed;
    }

    /**
     * @return boolean
     */
    public function isSkipped() {
        return $this->skipped;
    }

    /**
     * @param boolean $skipped
     */
    public function setSkipped($skipped = true) {
        $this->skipped = $skipped;
    }
}