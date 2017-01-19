<?php
namespace rtens\steps2\domain\walk;

use rtens\steps2\domain\GoalIdentifier;
use rtens\steps2\domain\Path;
use rtens\steps2\domain\PathIdentifier;
use rtens\steps2\domain\Walk;
use rtens\udity\check\DomainSpecification;
use rtens\udity\utils\Time;

class RemainingStepsSpec extends DomainSpecification {

    function noPathChosen() {
        $walk = $this->whenProject(Walk::class);
        $this->assertEquals($walk->getNextStep(), null);
        $this->assertEquals($this->projection(Walk::class)->getRemainingUnits(), 0);
    }

    function noNextStep() {
        $this->given(Path::class, 'foo')->created(Time::at('today'));
        $this->givenThat('DidChoosePath', Walk::class)->with('path', new PathIdentifier('foo'));

        $walk = $this->whenProject(Walk::class);
        $this->assertEquals($walk->getNextStep(), null);
        $this->assertEquals($walk->getRemainingUnits(), 0);
    }

    function nextStep() {
        $this->given(Path::class, 'foo')->created(Time::at('today'));
        $this->given(Path::class, 'foo')->didPlanStep(new GoalIdentifier('one'), 2, false);
        $this->given(Path::class, 'foo')->didPlanStep(new GoalIdentifier('two'), 3.5, false);
        $this->givenThat('DidChoosePath', Walk::class)->with('path', new PathIdentifier('foo'));

        $walk = $this->whenProject(Walk::class);
        $this->assertEquals($walk->getNextStep()->getGoal(), new GoalIdentifier('one'));
        $this->assertEquals($walk->getRemainingUnits(), 5.5);
    }
}