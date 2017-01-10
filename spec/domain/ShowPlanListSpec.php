<?php
namespace rtens\steps2\domain;

use rtens\udity\check\DomainSpecification;
use rtens\udity\utils\Time;

class ShowPlanListSpec extends DomainSpecification {

    function noPlans() {
        $this->whenProject(PlanList::class);
        $this->assert()->equals($this->projection(PlanList::class)->getList(), []);
    }

    function activePlan() {
        $this->given(Plan::class)->created(Time::at('today'));
        $this->whenProject(PlanList::class);
        $plans = $this->projection(PlanList::class)->getList();
        $this->assert()->equals(count($plans), 1);
    }

    function inactivePlan() {
        $this->given(Plan::class)->created(Time::at('tomorrow'));
        $this->whenProject(PlanList::class);
        $plans = $this->projection(PlanList::class)->getList();
        $this->assert()->equals(count($plans), 0);
    }
}