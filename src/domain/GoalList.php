<?php
namespace rtens\steps2\domain;

use rtens\udity\domain\objects\DomainObjectList;

class GoalList extends DomainObjectList {

    /**
     * @return \rtens\udity\Projection[]|Goal[]
     */
    public function getList() {
        return array_values(array_filter($this->getGoals(), function (Goal $goal) {
            return $goal->isOpen();
        }));
    }

    /**
     * @return \rtens\udity\Projection[]|Goal[]
     */
    protected function getGoals() {
        return parent::getList();
    }

}