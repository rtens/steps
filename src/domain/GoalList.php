<?php
namespace rtens\steps2\domain;

use rtens\udity\Event;
use rtens\udity\Projection;
use rtens\udity\utils\Time;

class GoalList implements Projection {
    /**
     * @var PathList
     */
    private $paths;
    /**
     * @var RankedGoal[]
     */
    private $goals = [];

    public function __construct() {
        $this->paths = new PathList();
    }

    public function apply(Event $event) {
        $this->paths->apply($event);

        $key = $event->getAggregateIdentifier()->getKey();
        if ($event->getAggregateIdentifier() instanceof GoalIdentifier) {
            if (!array_key_exists($key, $this->goals)) {
                $this->goals[$key] = new RankedGoal(new Goal($event->getAggregateIdentifier()), $this);
            }
        }

        foreach ($this->goals as $goal) {
            $goal->apply($event);
        }
    }

    /**
     * @return \rtens\udity\Projection[]|Goal[]
     */
    public function getList() {
        return array_values(array_filter($this->goals, function (RankedGoal $goal) {
            return
                $goal->isOpen()
                && !$this->hasUpcomingStep($goal)
                && !$goal->hasOpenLinks()
                && !$goal->hasOpenChildren();
        }));
    }

    /**
     * @param GoalIdentifier $identifier
     * @return \rtens\udity\Projection|RankedGoal
     */
    public function goal(GoalIdentifier $identifier) {
        return $this->goals[$identifier->getKey()];
    }

    public function options() {
        $items = $this->goals;

        uasort($items, function (RankedGoal $a, RankedGoal $b) {
            if ($a->isOpen() == $b->isOpen()) {
                return strcmp($a->getFullName(), $b->getFullName());
            }
            return $a->isOpen() ? -1 : 1;
        });

        return array_map(function (RankedGoal $goal) {
            return $goal->getFullName();
        }, $items);
    }

    private function hasUpcomingStep(RankedGoal $goal) {
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
}