<?php
namespace rtens\steps2\domain;

use rtens\udity\AggregateIdentifier;
use rtens\udity\Event;
use rtens\udity\utils\ArgumentFiller;
use rtens\udity\utils\Time;

class RankedGoal {
    /**
     * @var Goal
     */
    private $goal;
    /**
     * @var GoalList
     */
    private $list;

    public function __construct(Goal $goal, GoalList $list) {
        $this->goal = $goal;
        $this->list = $list;
    }

    public function apply(Event $event) {
        if ($event->getAggregateIdentifier() == $this->goal->getIdentifier()) {
            $this->goal->apply($event);
        }

        $this->invokeEventHandler($event);
    }

    /**
     * @return GoalIdentifier
     */
    public function getIdentifier() {
        return $this->goal->getIdentifier();
    }

    /**
     * @return string
     */
    public function getParents() {
        $parent = $this->getParent();
        if (!$parent) {
            return null;
        }

        return $parent->getParents() . $parent->getName() . ': ';
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->goal->getName();
    }

    public function getRating() {
        $rating = $this->goal->getRating();

        if ($rating != null) {
            return $rating;
        }
        if ($this->getParent()) {
            return $this->getParent()->getRating();
        }

        return null;
    }

    public function getLeft() {
        $left = $this->goal->getDaysLeft();

        if ($left !== null) {
            return $left;
        } else if ($this->getParent()) {
            return $this->getParent()->getLeft();
        }

        return null;
    }

    public function getNeglect() {
        $step = $this->getLastCompletedStep();

        if ($step !== null) {
            return (Time::now()->getTimestamp() - $step->getCompleted()->getTimestamp()) / 86400;
        } else if ($this->getParent()) {
            return $this->getParent()->getNeglect();
        }

        return null;
    }

    public function getQuota() {
        return $this->goal->getQuota();
    }

    public function getRank() {
        $ratingFactor = 0;
        $rating = $this->getRating();
        if ($rating) {
            $ratingFactor = $rating->getUrgency() * 2 + $rating->getImportance();
        }

        $panicFactor = 0;
        $left = $this->getLeft();
        if ($left !== null) {
            $panicFactor = min(30, max(37 - $left, 0));
        }

        $penalty = 0;
        $daysNeglected = $this->getNeglect();
        if ($daysNeglected !== null) {
            $penalty = min(30, max($daysNeglected - 7, 0));
        }

        $lackFactor = 1;
        $lack = $this->getLack();
        if ($lack !== null) {
            $lackFactor = max(0, $lackFactor + $lack);
        }

        return ($ratingFactor + $penalty) * $lackFactor + $panicFactor;
    }

    public function getFullName() {
        return $this->getParents() . $this->getName();
    }

    public function isOpen() {
        $parent = $this->getParent();
        if ($parent && !$parent->isOpen()) {
            return false;
        }

        return $this->goal->isOpen();
    }

    public function hasOpenLinks() {
        foreach ($this->goal->getLinks() as $link) {
            if ($this->list->goal($link)->isOpen()) {
                return true;
            }
        }
        return false;
    }

    public function hasOpenChildren() {
        foreach ($this->getChildren() as $child) {
            if ($child->isOpen()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return RankedGoal[]
     */
    public function getChildren() {
        return array_filter($this->list->getGoals(), function (RankedGoal $goal) {
            $parent = $goal->getParent();
            return $parent && $parent->getIdentifier() == $this->getIdentifier();
        });
    }

    private function getLastCompletedStep() {
        /** @var null|Step $lastCompletedStep */
        $lastCompletedStep = null;

        foreach ($this->list->paths() as $path) {
            foreach ($path->getCompletedSteps() as $step) {
                if ($step->getGoal() != $this->getIdentifier()) {
                    continue;
                }
                if (!$lastCompletedStep || $lastCompletedStep->getCompleted() < $step->getCompleted()) {
                    $lastCompletedStep = $step;
                }
            }
        }

        foreach ($this->getChildren() as $child) {
            $lastStepOfChild = $child->getLastCompletedStep();
            if ($lastStepOfChild && (!$lastCompletedStep || $lastCompletedStep->getCompleted() < $lastStepOfChild->getCompleted())) {
                $lastCompletedStep = $lastStepOfChild;
            }
        }

        return $lastCompletedStep;
    }

    public function getLack() {
        $quota = $this->getQuota();
        if (!$quota) {
            if (!$this->getParent()) {
                return null;
            }

            return $this->getParent()->getLack();
        }

        $proportion = $this->getNormalizedQuota() / $this->getNormalizedQuotaSum();

        $trackedHours = $this->getTrackedHours(Time::at($quota->getPerDays() . ' days ago'));
        return (($quota->getHours() - $trackedHours) / $quota->getHours()) * $proportion;
    }

    private function getNormalizedQuotaSum() {
        $sum = 0;
        foreach ($this->list->getGoals() as $goal) {
            if (!$goal->isOpen()) {
                continue;
            }
            $sum += $goal->getNormalizedQuota();
        }
        return $sum;
    }

    private function getNormalizedQuota() {
        $quota = $this->getQuota();
        if (!$quota) {
            return null;
        }
        return $quota->getHours() / $quota->getPerDays();
    }

    private function getTrackedHours(\DateTimeImmutable  $after) {
        $trackedSeconds = 0;

        foreach ($this->list->paths() as $path) {
            foreach ($path->getCompletedSteps() as $step) {
                if ($step->getGoal() != $this->getIdentifier()) {
                    continue;
                }
                if ($step->getCompleted() < $after) {
                    continue;
                }
                if ($step->getStarted() < $after) {
                    $trackedSeconds += $step->getCompleted()->getTimestamp() - $after->getTimestamp();
                } else {
                    $trackedSeconds += $step->getCompleted()->getTimestamp() - $step->getStarted()->getTimestamp();
                }
            }
        }
        $trackedHours = $trackedSeconds / 3600;

        foreach ($this->getChildren() as $child) {
            $trackedHours += $child->getTrackedHours($after);
        }

        return $trackedHours;
    }

    /**
     * @return null|RankedGoal
     */
    private function getParent() {
        if (!$this->goal->getParent()) {
            return null;
        }
        return $this->list->goal($this->goal->getParent());
    }

    private function invokeEventHandler(Event $event) {
        $aggregateClass = new \ReflectionClass($event->getAggregateIdentifier()->getName());
        $method = 'apply' . $aggregateClass->getShortName() . $event->getName();

        if (!method_exists($this, $method)) {
            return;
        }

        ArgumentFiller::from($this, $method)
            ->inject(Event::class, $event)
            ->inject(AggregateIdentifier::class, $event->getAggregateIdentifier())
            ->invoke($this, $event->getPayload());
    }
}