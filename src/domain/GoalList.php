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
}