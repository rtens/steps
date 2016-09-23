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
     * @var Step[]
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
     */
    public function __construct(GoalIdentifier $goal, $name) {
        $this->name = $name;
        $this->goal = $goal;
    }

    /**
     * @return Step[]
     */
    public function getSteps() {
        return array_values($this->steps);
    }

    /**
     * @return null|string
     */
    public function getNextStep() {
        if (!$this->steps) {
            return null;
        }
        return $this->getSteps()[0]->getDescription();
    }

    /**
     * @param Step $step
     */
    public function addStep(Step $step) {
        $this->steps[(string)$step->getStep()] = $step;
    }

    /**
     * @param StepIdentifier $step
     */
    public function removeStep(StepIdentifier $step) {
        unset($this->steps[(string)$step]);
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