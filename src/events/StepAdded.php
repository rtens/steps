<?php namespace rtens\steps\events;
use rtens\steps\model\GoalIdentifier;
use rtens\steps\model\StepIdentifier;

class StepAdded {
    /**
     * @var StepIdentifier
     */
    private $step;
    /**
     * @var GoalIdentifier
     */
    private $goal;
    /**
     * @var string
     */
    private $description;

    /**
     * @param StepIdentifier $step
     * @param GoalIdentifier $goal
     * @param string $description
     */
    public function __construct(StepIdentifier $step, GoalIdentifier $goal, $description) {
        $this->step = $step;
        $this->goal = $goal;
        $this->description = $description;
    }

    /**
     * @return StepIdentifier
     */
    public function getStep() {
        return $this->step;
    }

    /**
     * @return GoalIdentifier
     */
    public function getGoal() {
        return $this->goal;
    }

    /**
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }
}