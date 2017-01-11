<?php
namespace rtens\steps2\domain;

use rtens\udity\check\DomainSpecification;
use rtens\udity\utils\Time;

class WalkOnPathSpec extends DomainSpecification {

    function noActivePlans() {
        $this->whenProject(Walk::class)
            ->assertEquals($this->projection(Walk::class)->hasActivePlan(), false);
    }

    function activePlan() {
        $this->given(Path::class)->created(Time::at('today'));
        $this->whenProject(Walk::class)
            ->assertEquals($this->projection(Walk::class)->hasActivePlan(), true);
    }

    function inactivePlan() {
        $this->given(Path::class)->created(Time::at('tomorrow'));
        $this->whenProject(Walk::class)
            ->assertEquals($this->projection(Walk::class)->hasActivePlan(), false);
    }

    function activeAndInactivePlan() {
        $this->given(Path::class, 'a')->created(Time::at('today'));
        $this->given(Path::class, 'b')->created(Time::at('tomorrow'));
        $this->whenProject(Walk::class)
            ->assertEquals($this->projection(Walk::class)->hasActivePlan(), true);
    }

    function noSteps() {
        $this->given(Path::class)->created(Time::at('today'));
        $this->whenProject(Walk::class)
            ->assertEquals($this->projection(Walk::class)->hasNextStep(), false);
    }

    function withStep() {
        $this->given(Path::class)->created(Time::at('today'));
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('Foo'), 1, false);
        $this->whenProject(Walk::class)
            ->assertEquals($this->projection(Walk::class)->hasNextStep(), true);
    }

    function noCurrentStep() {
        $this->given(Path::class)->created(Time::at('today'));
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('Foo'), 1, false);
        $this->whenProject(Walk::class)
            ->assertEquals($this->projection(Walk::class)->isTakingStep(), false);
    }

    function withCurrentStep() {
        $this->given(Path::class)->created(Time::at('today'));
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('Foo'), 1, false);
        $this->given(Path::class)->didTakeNextStep();
        $this->whenProject(Walk::class)
            ->assertEquals($this->projection(Walk::class)->isTakingStep(), true);
    }
}