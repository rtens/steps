<?php
namespace rtens\steps2\domain;

use rtens\udity\domain\objects\DomainObject;
use rtens\udity\Event;
use rtens\udity\utils\Time;

class Path extends DomainObject {
    /**
     * @var \DateTimeImmutable
     */
    private $starts;
    /**
     * @var \DateTimeImmutable
     */
    private $ends;
    /**
     * @var Step[]
     */
    private $steps = [];

    public static function defaultEnd(\DateTimeImmutable $starts) {
        return $starts->add(new \DateInterval('P1D'));
    }

    public function created(\DateTimeImmutable $starts, \DateTimeImmutable $ends = null) {
        $this->starts = $starts;
        $this->ends = $ends;
    }

    public function caption() {
        $caption = $this->starts->format('D Y-m-d');

        if ($this->ends) {
            $caption .= ' - ' . $this->ends->format('D Y-m-d');
        }

        if ($this->isActive()) {
            $caption .= ' (active)';
        }

        return $caption;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getStarts() {
        return $this->starts;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getEnds() {
        return $this->ends ?: self::defaultEnd($this->starts);
    }

    public function isActive() {
        return $this->getStarts() < Time::now() && Time::now() < $this->getEnds();
    }

    public function getCurrentStep() {
        foreach ($this->steps as $step) {
            if ($step->getStarted() && !$step->getCompleted()) {
                return $step;
            }
        }
        return null;
    }

    /**
     * @return Step[]
     */
    public function getRemainingSteps() {
        return array_values(array_filter($this->steps, function (Step $step) {
            return !$step->getStarted();
        }));
    }

    /**
     * @return Step[]
     */
    public function getCompletedSteps() {
        return array_values(array_filter($this->steps, function (Step $step) {
            return !!$step->getCompleted();
        }));
    }

    /**
     * @param GoalIdentifier $goal
     * @param float $units
     * @param bool $splitIntoUnits
     */
    public function doPlanStep(GoalIdentifier $goal, $units = 1.0, $splitIntoUnits = true) {
    }

    public function didPlanStep(GoalIdentifier $goal, $units, $splitIntoUnits) {
        while ($units > 0) {
            $decrement = $splitIntoUnits ? min(1, $units) : $units;
            $units -= $decrement;

            $this->steps[] = new Step($goal, $decrement);
        }
    }

    public function doTakeNextStep() {
        if ($this->getCurrentStep()) {
            throw new \Exception('Already taking a step');
        } else if (!$this->getRemainingSteps()) {
            throw new \Exception('No next step to start');
        }
    }

    public function didTakeNextStep(Event $event) {
        $this->getRemainingSteps()[0]->setStarted($event->getWhen());
    }

    public function doCompleteStep() {
        if (!$this->getCurrentStep()) {
            throw new \Exception('Not taking any step');
        }
    }

    public function didCompleteStep(Event $event) {
        $this->getCurrentStep()->setCompleted($event->getWhen());
    }
}