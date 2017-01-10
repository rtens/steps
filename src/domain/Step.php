<?php
namespace rtens\steps2\domain;

class Step {
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
}