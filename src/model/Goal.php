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
     * @var StepIdentifier[]
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
     * @return StepIdentifier[]
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

    /**
     * @param StepIdentifier $step
     */
    public function removeStep(StepIdentifier $step) {
        $this->steps = array_values(array_diff($this->steps, [$step]));
    }
}