<?php
namespace rtens\steps2\domain;

use rtens\udity\check\DomainSpecification;
use rtens\udity\utils\Time;

class ShowPathsSpec extends DomainSpecification {

    function noPlans() {
        $this->whenProject(PathList::class);
        $this->assert()->equals($this->projection(PathList::class)->getList(), []);
    }

    function activePlan() {
        $this->given(Path::class)->created(Time::at('today'));
        $this->whenProject(PathList::class);
        $plans = $this->projection(PathList::class)->getList();
        $this->assert()->equals(count($plans), 1);
    }

    function inactivePlan() {
        $this->given(Path::class)->created(Time::at('tomorrow'));
        $this->whenProject(PathList::class);
        $plans = $this->projection(PathList::class)->getList();
        $this->assert()->equals(count($plans), 0);
    }
}