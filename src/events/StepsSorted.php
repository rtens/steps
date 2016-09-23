<?php namespace rtens\steps\events;
use rtens\steps\model\GoalIdentifier;
use rtens\steps\model\StepIdentifier;

class StepsSorted {
    /**
     * @var GoalIdentifier
     */
    private $goal;
    /**
     * @var array|StepIdentifier[]
     */
    private $steps;
    /**
     * @var \DateTime
     */
    private $when;

    /**
     * @param GoalIdentifier $goal
     * @param StepIdentifier[] $steps
     * @param \DateTime $when
     */
    public function __construct(GoalIdentifier $goal, array $steps, \DateTime $when) {
        $this->goal = $goal;
        $this->steps = $steps;
        $this->when = $when;
    }

    /**
     * @return GoalIdentifier
     */
    public function getGoal() {
        return $this->goal;
    }

    /**
     * @return array|StepIdentifier[]
     */
    public function getSteps() {
        return $this->steps;
    }

    /**
     * @return \DateTime
     */
    public function getWhen() {
        return $this->when;
    }
}