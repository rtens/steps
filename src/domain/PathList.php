<?php
namespace rtens\steps2\domain;

use rtens\udity\domain\objects\DomainObjectList;

class PathList extends DomainObjectList {

    /**
     * @return \rtens\udity\Projection[]|Path[]
     */
    public function getList() {
        return array_filter($this->getPlans(), function (Path $plan) {
            return $plan->isActive();
        });
    }

    /**
     * @return \rtens\udity\Projection[]|Path[]
     */
    protected function getPlans() {
        return parent::getList();
    }

}