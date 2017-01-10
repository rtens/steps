<?php
namespace rtens\steps2\domain;

use rtens\udity\check\DomainSpecification;
use rtens\udity\utils\Time;

class GoSpec extends DomainSpecification {

    function noActivePlans() {
        $this->whenProject(Go::class)
            ->assertEquals($this->projection(Go::class)->hasActivePlan(), false);
    }

    function activePlan() {
        $this->given(Plan::class)->created(Time::at('today'));
        $this->whenProject(Go::class)
            ->assertEquals($this->projection(Go::class)->hasActivePlan(), true);
    }

    function inactivePlan() {
        $this->given(Plan::class)->created(Time::at('tomorrow'));
        $this->whenProject(Go::class)
            ->assertEquals($this->projection(Go::class)->hasActivePlan(), false);
    }

    function activeAndInactivePlan() {
        $this->given(Plan::class, 'a')->created(Time::at('today'));
        $this->given(Plan::class, 'b')->created(Time::at('tomorrow'));
        $this->whenProject(Go::class)
            ->assertEquals($this->projection(Go::class)->hasActivePlan(), true);
    }

    function noSteps() {
        $this->given(Plan::class)->created(Time::at('today'));
        $this->whenProject(Go::class)
            ->assertEquals($this->projection(Go::class)->hasNextStep(), false);
    }

    function withStep() {
        $this->given(Plan::class)->created(Time::at('today'));
        $this->given(Plan::class)->didPlanStep(new GoalIdentifier('Foo'), 1, false);
        $this->whenProject(Go::class)
            ->assertEquals($this->projection(Go::class)->hasNextStep(), true);
    }

    function noCurrentStep() {
        $this->given(Plan::class)->created(Time::at('today'));
        $this->given(Plan::class)->didPlanStep(new GoalIdentifier('Foo'), 1, false);
        $this->whenProject(Go::class)
            ->assertEquals($this->projection(Go::class)->isTakingStep(), false);
    }

    function withCurrentStep() {
        $this->given(Plan::class)->created(Time::at('today'));
        $this->given(Plan::class)->didPlanStep(new GoalIdentifier('Foo'), 1, false);
        $this->given(Plan::class)->didStartNextStep();
        $this->whenProject(Go::class)
            ->assertEquals($this->projection(Go::class)->isTakingStep(), true);
    }
}