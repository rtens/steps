<?php namespace rtens\steps\projecting;

use rtens\domin\parameters\Html;
use rtens\steps\model\GoalIdentifier;
use rtens\steps\model\StepIdentifier;

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
     * @var Html[]
     */
    private $notes = [];
    private $importance;
    private $urgency;
    private $deadline;

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
     * @return null|StepIdentifier
     */
    public function getNextStep() {
        if (!$this->steps) {
            return null;
        }
        return $this->steps[0];
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

    /**
     * @return Html[]
     */
    public function getNotes() {
        return $this->notes;
    }

    /**
     * @param string $note
     */
    public function addNote($note) {
        $this->notes[] = new Html($note);
    }

    public function getImportance() {
        return $this->importance;
    }

    public function getUrgency() {
        return $this->urgency;
    }

    public function setRating($importance, $urgency) {
        $this->importance = $importance;
        $this->urgency = $urgency;
    }

    public function getDeadline() {
        return $this->deadline;
    }

    public function setDeadline(\DateTime $deadline) {
        $this->deadline = $deadline;
    }
}