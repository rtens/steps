<?php
namespace rtens\steps2\domain;

use rtens\udity\domain\objects\DomainObjectList;

class PlanList extends DomainObjectList {

    /**
     * @return \rtens\udity\Projection[]|Plan[]
     */
    public function getList() {
        return array_filter(parent::getList(), function (Plan $plan) {
            return $plan->isActive();
        });
    }

}