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
            return
                $goal->isOpen()
                && !$this->hasUpcomingStep($goal)
                && !$this->hasOpenLinks($goal)
                && !$this->hasOpenChildren($goal);
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

    public function applyDidMove(Event $event, GoalIdentifier $parent = null) {
        $this->parents[$event->getAggregateIdentifier()->getKey()] = $parent ? $parent->getKey() : null;
    }

    private function hasUpcomingStep(Goal $goal) {
        foreach ($this->paths->getItems() as $path) {
            if ($path->getEnds() < Time::now()) {
                continue;
            }

            foreach ($path->getRemainingSteps() as $step) {
                if ($step->getGoal() == $goal->getIdentifier()) {
                    return true;
                }
            }
        }
        return false;
    }

    private function hasOpenLinks(Goal $goal) {
        foreach ($goal->getLinks() as $link) {
            if ($this->getItems()[$link->getKey()]->isOpen()) {
                return true;
            }
        }
        return false;
    }

    private function hasOpenChildren(Goal $goal) {
        foreach ($this->parents as $child => $parent) {
            if ($parent == $goal->getIdentifier()->getKey() && $this->getItems()[$child]->isOpen()) {
                return true;
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
        $lineage = [];

        $current = $goal->getIdentifier()->getKey();
        while ($current) {
            array_unshift($lineage, $this->getItems()[$current]->caption());
            if (!array_key_exists($current, $this->parents)) {
                $current = null;
            } else {
                $current = $this->parents[$current];
            }
        }
        return implode(': ', $lineage);
    }
}