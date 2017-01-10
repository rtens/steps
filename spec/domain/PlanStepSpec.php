<?php
namespace rtens\steps2\domain;

use rtens\udity\check\DomainSpecification;
use rtens\udity\check\event\Events;

class PlanStepSpec extends DomainSpecification {

    function defaultUnits() {
        $this->when(Plan::class)->doPlanStep(new GoalIdentifier('MyGoal'));
        $this->then()->shouldCount(1);
        $this->then(Events::any()->with('units', 1))->shouldBeAppended();

        $this->whenProjectObject(Plan::class);
        $steps = $this->projection(Plan::class)->getNextSteps();
        $this->assert()->equals(count($steps), 1);
    }

    function bulkUnits() {
        $this->when(Plan::class)->doPlanStep(new GoalIdentifier('MyGoal'), 3, false);

        $this->whenProjectObject(Plan::class);
        $steps = $this->projection(Plan::class)->getNextSteps();
        $this->assert()->equals(count($steps), 1);
        $this->assert()->equals($steps[0]->getUnits(), 3);
    }

    function multipleUnits() {
        $this->when(Plan::class)->doPlanStep(new GoalIdentifier('MyGoal'), 3);

        $this->whenProjectObject(Plan::class);
        $steps = $this->projection(Plan::class)->getNextSteps();
        $this->assert()->equals(count($steps), 3);
        $this->assert()->equals($steps[0]->getUnits(), 1);
        $this->assert()->equals($steps[1]->getUnits(), 1);
        $this->assert()->equals($steps[2]->getUnits(), 1);
    }

    function fractionalUnits() {
        $this->when(Plan::class)->doPlanStep(new GoalIdentifier('MyGoal'), 2.25);

        $this->whenProjectObject(Plan::class);
        $steps = $this->projection(Plan::class)->getNextSteps();
        $this->assert()->equals(count($steps), 3);
        $this->assert()->equals($steps[0]->getUnits(), 1);
        $this->assert()->equals($steps[1]->getUnits(), 1);
        $this->assert()->equals($steps[2]->getUnits(), .25);
    }
}