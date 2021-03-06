<?php
namespace rtens\steps2\domain;

use rtens\udity\check\DomainSpecification;
use rtens\udity\check\event\Events;
use rtens\udity\utils\Time;

class PlanStepSpec extends DomainSpecification {

    function before() {
        $this->given(Path::class)->created(Time::at('today'));
    }

    function defaultUnits() {
        $this->when(Path::class)->doPlanStep(new GoalIdentifier('MyGoal'));
        $this->then()->shouldCount(1);
        $this->then(Events::any()->with('units', 1))->shouldBeAppended();

        $this->whenProjectObject(Path::class);
        $steps = $this->projection(Path::class)->getRemainingSteps();
        $this->assert()->equals(count($steps), 1);
    }

    function bulkUnits() {
        $this->when(Path::class)->doPlanStep(new GoalIdentifier('MyGoal'), 3, false);

        $this->whenProjectObject(Path::class);
        $steps = $this->projection(Path::class)->getRemainingSteps();
        $this->assert()->equals(count($steps), 1);
        $this->assert()->equals($steps[0]->getUnits(), 3);
    }

    function multipleUnits() {
        $this->when(Path::class)->doPlanStep(new GoalIdentifier('MyGoal'), 3);

        $this->whenProjectObject(Path::class);
        $steps = $this->projection(Path::class)->getRemainingSteps();
        $this->assert()->equals(count($steps), 3);
        $this->assert()->equals($steps[0]->getUnits(), 1);
        $this->assert()->equals($steps[1]->getUnits(), 1);
        $this->assert()->equals($steps[2]->getUnits(), 1);
    }

    function fractionalUnits() {
        $this->when(Path::class)->doPlanStep(new GoalIdentifier('MyGoal'), 2.25);

        $this->whenProjectObject(Path::class);
        $steps = $this->projection(Path::class)->getRemainingSteps();
        $this->assert()->equals(count($steps), 3);
        $this->assert()->equals($steps[0]->getUnits(), 1);
        $this->assert()->equals($steps[1]->getUnits(), 1);
        $this->assert()->equals($steps[2]->getUnits(), .25);
    }

    function doNotAddToOldPaths() {
        $this->given(Path::class, 'old')->created(Time::at('yesterday'));
        $this->tryTo(Path::class, 'old')->doPlanStep(new GoalIdentifier('Foo'));
        $this->thenShouldFailWith('Steps can only be planned for active or upcoming paths');
    }
}