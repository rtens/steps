<?php namespace rtens\steps\projecting;

use rtens\domin\parameters\Html;
use rtens\steps\events\DeadlineSet;
use rtens\steps\events\GoalAchieved;
use rtens\steps\events\GoalCreated;
use rtens\steps\events\GoalRated;
use rtens\steps\events\NoteAdded;
use rtens\steps\events\StepAdded;
use rtens\steps\events\StepCompleted;
use rtens\steps\events\StepsSorted;
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
    private $steps = [];
    /**
     * @var Html[]
     */
    private $notes = [];
    /**
     * @var float
     */
    private $importance;
    /**
     * @var float
     */
    private $urgency;
    /**
     * @var \DateTime
     */
    private $deadline;
    /**
     * @var null|\DateTime
     */
    private $achieved;
    /**
     * @var StepIdentifier[]
     */
    private $sorted = [];

    /**
     * @param GoalIdentifier $goal
     */
    public function __construct(GoalIdentifier $goal) {
        $this->goal = $goal;
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
     * @return int
     */
    public function getStepCount() {
        return count($this->getSteps());
    }

    /**
     * @return null|string
     */
    public function getNextStep() {
        if (!$this->getSteps()) {
            return null;
        }
        return $this->getSteps()[0]->getDescription();
    }

    /**
     * @return Step[]
     */
    public function getSteps() {
        $filtered = array_values(array_filter($this->steps, function (Step $step) {
            return !$step->isCompleted();
        }));
        $original = array_values(array_map(function (Step $step) {
            return $step->getStep();
        }, $filtered));

        if ($this->sorted) {
            usort($filtered, function (Step $a, Step $b) use ($original) {
                if (in_array($a->getStep(), $this->sorted) && !in_array($b->getStep(), $this->sorted)) {
                    return -1;
                } else if (!in_array($a->getStep(), $this->sorted) && in_array($b->getStep(), $this->sorted)) {
                    return 1;
                } else if (in_array($a->getStep(), $this->sorted) && in_array($b->getStep(), $this->sorted)) {
                    return array_search($a->getStep(), $this->sorted) - array_search($b->getStep(), $this->sorted);
                } else {
                    return array_search($a->getStep(), $original) - array_search($b->getStep(), $original);
                }
            });
        }

        return array_values($filtered);
    }

    /**
     * @return \rtens\domin\parameters\Html[]
     */
    public function getNotes() {
        return array_map(function ($note) {
            return new Html($note);
        }, $this->notes);
    }

    /**
     * @return float
     */
    public function getImportance() {
        return $this->importance;
    }

    /**
     * @return float
     */
    public function getUrgency() {
        return $this->urgency;
    }

    /**
     * @return \DateTime
     */
    public function getDeadline() {
        return $this->deadline;
    }

    /**
     * @return bool
     */
    public function isAchieved() {
        return !!$this->achieved;
    }

    public function applyGoalCreated(GoalCreated $e) {
        if ($this->goal != $e->getGoal()) {
            return;
        }
        $this->name = $e->getName();
    }

    public function applyStepAdded(StepAdded $e) {
        if ($this->goal != $e->getGoal()) {
            return;
        }
        $this->steps[(string)$e->getStep()] = new Step($e->getStep(), $e->getDescription());
    }

    public function applyStepCompleted(StepCompleted $e) {
        if (!array_key_exists((string)$e->getStep(), $this->steps)) {
            return;
        }
        $this->steps[(string)$e->getStep()]->setCompleted($e->getWhen());
    }

    public function applyGoalAchieved(GoalAchieved $e) {
        if ($this->goal != $e->getGoal()) {
            return;
        }
        $this->achieved = $e->getWhen();
    }

    public function applyNoteAdded(NoteAdded $e) {
        if (!$this->goal == $e->getGoal()) {
            return;
        }
        $this->notes[] = $e->getNote();
    }

    public function applyGoalRated(GoalRated $e) {
        if (!$this->goal == $e->getGoal()) {
            return;
        }
        $this->importance = $e->getImportance();
        $this->urgency = $e->getUrgency();
    }

    public function applyDeadlineSet(DeadlineSet $e) {
        if (!$this->goal == $e->getGoal()) {
            return;
        }
        $this->deadline = $e->getDeadline();
    }

    public function applyStepsSorted(StepsSorted $e) {
        if (!$this->goal == $e->getGoal()) {
            return;
        }
        $this->sorted = $e->getSteps();
    }
}