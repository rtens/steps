<?php
namespace rtens\steps2\domain;

use rtens\udity\AggregateIdentifier;
use rtens\udity\Event;
use rtens\udity\utils\ArgumentFiller;

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

        $parentGoal = $this->list->goal($parent);
        return $parentGoal->getParents() . $parentGoal->getName() . ': ';
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->goal->getName();
    }

    public function getFullName() {
        return $this->getParents() . $this->getName();
    }

    public function isOpen() {
        $parent = $this->getParent();
        if ($parent && !$this->list->goal($parent)->isOpen()) {
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
            return $goal->getParent() == $this->getIdentifier();
        });
    }

    /**
     * @return null|GoalIdentifier
     */
    private function getParent() {
        return $this->goal->getParent();
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