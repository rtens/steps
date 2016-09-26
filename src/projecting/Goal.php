<?php namespace rtens\steps\projecting;

use rtens\domin\parameters\Html;
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
use rtens\steps\model\BlockIdentifier;
use rtens\steps\model\GoalIdentifier;
use rtens\steps\model\StepIdentifier;
use rtens\steps\model\Steps;
use rtens\steps\model\Time;

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
     * @var \DateTime
     */
    private $lastActivity;
    /**
     * @var BlockIdentifier[]
     */
    private $blocks = [];

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
        $this->lastActivity = $e->getWhen();
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
        if ($this->goal != $e->getGoal()) {
            return;
        }
        $this->notes[] = $e->getNote();
    }

    public function applyGoalRated(GoalRated $e) {
        if ($this->goal != $e->getGoal()) {
            return;
        }
        $this->importance = $e->getImportance();
        $this->urgency = $e->getUrgency();
    }

    public function applyDeadlineSet(DeadlineSet $e) {
        if ($this->goal != $e->getGoal()) {
            return;
        }
        $this->deadline = $e->getDeadline();
    }

    public function applyStepsSorted(StepsSorted $e) {
        if ($this->goal != $e->getGoal()) {
            return;
        }
        $this->sorted = $e->getSteps();
    }

    public function applyBlockPlanned(BlockPlanned $e) {
        if ($this->goal != $e->getGoal()) {
            return;
        }
        $this->blocks[] = $e->getBlock();
    }

    public function applyBlockFinished(BlockFinished $e) {
        if (!in_array($e->getBlock(), $this->blocks)) {
            return;
        }
        $this->lastActivity = $e->getWhen();
    }

    public function getLastActivity() {
        return $this->lastActivity;
    }

    public function getDaysLeft() {
        if (!$this->deadline) {
            return null;
        }
        return ($this->deadline->getTimestamp() - Time::now()->getTimestamp()) / (24 * 3600);
    }

    public function getRank() {
        $effectiveUrgency = $this->calculateEffectiveUrgency();

        $penaltySeconds = Time::now()->getTimestamp() - $this->lastActivity->getTimestamp();
        $penalty = ($penaltySeconds) / (24 * 60 * 60);

        return $this->importance + 2 * $effectiveUrgency + $penalty;
    }

    private function calculateEffectiveUrgency() {
        if (!$this->deadline) {
            return $this->urgency;
        }

        $effectiveUrgency = $this->urgency;
        $timeLeft = $this->deadline->getTimestamp() - Time::now()->getTimestamp();

        if ($timeLeft <= 0) {
            $effectiveUrgency = Steps::MAX_URGENCY;
            return $effectiveUrgency;
        } else if ($timeLeft < Steps::DEADLINE_ZONE_SECONDS) {
            $proportionLeft = (Steps::DEADLINE_ZONE_SECONDS - $timeLeft) / Steps::DEADLINE_ZONE_SECONDS;
            $deltaUrgency = Steps::MAX_URGENCY - $effectiveUrgency;
            $effectiveUrgency += $proportionLeft * $deltaUrgency;
            return $effectiveUrgency;
        }
        return $effectiveUrgency;
    }
}