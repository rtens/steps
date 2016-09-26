<?php namespace rtens\steps\projecting;

use rtens\steps\events\BlockFinished;
use rtens\steps\events\BlockPlanned;
use rtens\steps\events\DeadlineSet;
use rtens\steps\events\GoalAchieved;
use rtens\steps\events\GoalCreated;
use rtens\steps\events\GoalRated;
use rtens\steps\events\NoteAdded;
use rtens\steps\events\StepAdded;
use rtens\steps\events\StepCompleted;
use rtens\steps\events\StepsSorted;
use rtens\steps\model\GoalIdentifier;
use rtens\steps\model\Time;

class GoalList {
    /**
     * @var Goal[]
     */
    private $goals = [];
    /**
     * @var GoalIdentifier[]
     */
    private $plan = [];
    /**
     * @var boolean
     */
    private $showPlanned;

    /**
     * @param bool $showPlanned
     */
    public function __construct($showPlanned) {
        $this->showPlanned = $showPlanned;
    }

    /**
     * @return Goal[]
     */
    public function getGoals() {
        $filtered = array_filter($this->goals, function (Goal $goal) {
            return
                !$goal->isAchieved()
                && ($this->showPlanned || !in_array($goal->getGoal(), $this->plan));
        });

        usort($filtered, function (Goal $a, Goal $b) {
            return $a->getRank() > $b->getRank() ? -1 : 1;
        });

        return array_values($filtered);
    }

    private function apply($function, $event) {
        foreach ($this->goals as $goal) {
            call_user_func([$goal, $function], $event);
        }
    }

    public function applyGoalCreated(GoalCreated $e) {
        $this->goals[] = new Goal($e->getGoal());
        $this->apply(__FUNCTION__, $e);
    }

    public function applyStepAdded(StepAdded $e) {
        $this->apply(__FUNCTION__, $e);
    }

    public function applyStepCompleted(StepCompleted $e) {
        $this->apply(__FUNCTION__, $e);
    }

    public function applyGoalAchieved(GoalAchieved $e) {
        $this->apply(__FUNCTION__, $e);
    }

    public function applyNoteAdded(NoteAdded $e) {
        $this->apply(__FUNCTION__, $e);
    }

    public function applyGoalRated(GoalRated $e) {
        $this->apply(__FUNCTION__, $e);
    }

    public function applyDeadlineSet(DeadlineSet $e) {
        $this->apply(__FUNCTION__, $e);
    }

    public function applyStepsSorted(StepsSorted $e) {
        $this->apply(__FUNCTION__, $e);
    }

    public function applyBlockPlanned(BlockPlanned $e) {
        if ($e->getWhen()->setTime(0, 0) == Time::at('today')) {
            $this->plan[] = $e->getGoal();
        }
        $this->apply(__FUNCTION__, $e);
    }

    public function applyBlockFinished(BlockFinished $e) {
        $this->apply(__FUNCTION__, $e);
    }
}