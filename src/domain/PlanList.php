<?php
namespace rtens\steps2\domain;

use rtens\udity\domain\objects\DomainObjectList;

class PlanList extends DomainObjectList {

    /**
     * @return \rtens\udity\Projection[]|Plan[]
     */
    public function getList() {
        return array_filter($this->getPlans(), function (Plan $plan) {
            return $plan->isActive();
        });
    }

    /**
     * @return \rtens\udity\Projection[]|Plan[]
     */
    protected function getPlans() {
        return parent::getList();
    }

}