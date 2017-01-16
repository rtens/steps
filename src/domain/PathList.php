<?php
namespace rtens\steps2\domain;

use rtens\udity\domain\objects\DomainObjectList;

class PathList extends DomainObjectList {

    /**
     * @return \rtens\udity\Projection[]|Path[]
     */
    public function getList() {
        return array_filter(parent::getList(), function (Path $plan) {
            return $plan->isActive();
        });
    }

    /**
     * @return \rtens\udity\Projection[]|Path[]
     */
    protected function getItems() {
        return parent::getItems();
    }

    public function options() {
        $options = parent::options();
        uksort($options, function ($a, $b) {
            return $this->getItems()[$a]->isActive() ? -1 : ($this->getItems()[$b]->isActive() ? 1 : 0);
        });
        return $options;
    }
}