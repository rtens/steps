<?php namespace rtens\steps\projecting;

use rtens\steps\events\DeadlineSet;
use rtens\steps\events\GoalAchieved;
use rtens\steps\events\GoalCreated;
use rtens\steps\events\GoalRated;
use rtens\steps\events\NoteAdded;
use rtens\steps\events\StepAdded;
use rtens\steps\events\StepCompleted;
use rtens\steps\model\Goal;

class GoalList {
    /**
     * @var Goal[]
     */
    private $goals = [];

    /**
     * @var StepAdded[]
     */
    private $steps = [];

    /**
     * @return Goal[]
     */
    public function getGoals() {
        return array_values($this->goals);
    }

    public function applyGoalCreated(GoalCreated $e) {
        $this->goals[(string)$e->getGoal()] = new Goal($e->getGoal(), $e->getName());
    }

    public function applyStepAdded(StepAdded $e) {
        $this->goals[(string)$e->getGoal()]->addStep($e->getStep());
        $this->steps[(string)$e->getStep()] = $e;
    }

    public function applyStepCompleted(StepCompleted $e) {
        $goal = $this->steps[(string)$e->getStep()]->getGoal();
        $this->goals[(string)$goal]->removeStep($e->getStep());
    }

    public function applyGoalAchieved(GoalAchieved $e) {
        unset($this->goals[(string)$e->getGoal()]);
    }

    public function applyNoteAdded(NoteAdded $e) {
        $this->goals[(string)$e->getGoal()]->addNote($e->getNote());
    }

    public function applyGoalRated(GoalRated $e) {
        $this->goals[(string)$e->getGoal()]->setRating($e->getImportance(), $e->getUrgency());
    }

    public function applyDeadlineSet(DeadlineSet $e) {
        $this->goals[(string)$e->getGoal()]->setDeadline($e->getDeadline());
    }
}