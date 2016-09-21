<?php namespace rtens\steps\model;

class Goal {
    /**
     * @var GoalIdentifier
     */
    private $goal;
    /**
     * @var string
     */
    private $name;
    /**
     * @var array|StepIdentifier[]
     */
    private $steps;

    /**
     * @param GoalIdentifier $goal
     * @param string $name
     * @param StepIdentifier[] $steps
     */
    public function __construct(GoalIdentifier $goal, $name, $steps = []) {
        $this->name = $name;
        $this->goal = $goal;
        $this->steps = $steps;
    }

    /**
     * @return array|StepIdentifier[]
     */
    public function getSteps() {
        return $this->steps;
    }

    /**
     * @param StepIdentifier $step
     */
    public function addStep(StepIdentifier $step) {
        $this->steps[] = $step;
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
    public function getName() {
        return $this->name;
    }
}