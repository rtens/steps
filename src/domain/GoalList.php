<?php
namespace rtens\steps2\domain;

use rtens\udity\domain\objects\DomainObjectList;
use rtens\udity\Event;
use rtens\udity\utils\Time;

class GoalList extends DomainObjectList {
    /**
     * @var PathList
     */
    private $paths;
    /**
     * @var string[]
     */
    private $parents = [];

    public function __construct() {
        $this->paths = new PathList();
    }

    /**
     * @return \rtens\udity\Projection[]|Goal[]
     */
    public function getList() {
        return array_values(array_filter($this->getGoals(), function (Goal $goal) {
            return $goal->isOpen() && !$this->hasUpcomingStep($goal->getIdentifier());
        }));
    }

    /**
     * @return \rtens\udity\Projection[]|Goal[]
     */
    protected function getGoals() {
        return parent::getList();
    }

    public function apply(Event $event) {
        $this->paths->apply($event);
        return parent::apply($event);
    }

    public function applyDidMove(GoalIdentifier $parent, Event $event) {
        $this->parents[$event->getAggregateIdentifier()->getKey()] = $parent->getKey();
    }

    private function hasUpcomingStep(GoalIdentifier $goal) {
        foreach ($this->paths->getItems() as $path) {
            if ($path->getEnds() < Time::now()) {
                continue;
            }

            foreach ($path->getRemainingSteps() as $step) {
                if ($step->getGoal() == $goal) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @return \rtens\udity\Projection[]|Goal[]
     */
    protected function getItems() {
        return parent::getItems();
    }

    public function options() {
        $items = $this->getItems();
        uasort($items, function (Goal $a, Goal $b) {
            return $a->isOpen() ? -1 : ($b->isOpen() ? 1 : 0);
        });
        return array_map(function (Goal $goal) {
            return $this->fullName($goal);
        }, $items);
    }

    private function fullName(Goal $goal) {
        $lineage = [$goal->caption()];

        $current = $goal->getIdentifier()->getKey();
        while ($current) {
            if (!array_key_exists($current, $this->parents)) {
                $current = null;
            } else {
                $current = $this->parents[$current];
                array_unshift($lineage, $this->getItems()[$current]->caption());
            }
        }
        return implode(': ', $lineage);
    }
}