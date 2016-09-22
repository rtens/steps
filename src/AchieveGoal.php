<?php namespace rtens\steps;
use rtens\steps\model\GoalIdentifier;
use rtens\steps\model\Steps;
use rtens\steps\model\Time;
use watoki\karma\implementations\commandQuery\Command;

class AchieveGoal implements Command {
    /**
     * @var GoalIdentifier
     */
    private $goal;
    /**
     * @var \DateTime
     */
    private $when;

    /**
     * @param GoalIdentifier $goal
     * @param \DateTime|null $when
     */
    public function __construct(GoalIdentifier $goal, \DateTime $when = null) {
        $this->goal = $goal;
        $this->when = $when ?: Time::now();
    }

    /**
     * @return GoalIdentifier
     */
    public function getGoal() {
        return $this->goal;
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