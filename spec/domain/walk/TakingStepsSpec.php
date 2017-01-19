<?php
namespace rtens\steps2\domain\walk;

use rtens\steps2\domain\GoalIdentifier;
use rtens\steps2\domain\Path;
use rtens\steps2\domain\PathIdentifier;
use rtens\steps2\domain\Step;
use rtens\steps2\domain\Walk;
use rtens\udity\check\DomainSpecification;
use rtens\udity\utils\Time;

class TakingStepsSpec extends DomainSpecification {

    function before() {
        $this->given(Path::class, 'foo')->created(Time::at('today'));
    }

    function noPathChosen() {
        $this->whenProject(Walk::class);
        $this->assertEquals($this->projection(Walk::class)->getCurrentStep(), null);
    }

    function noCurrentStep() {
        $this->givenThat('DidChoosePath', Walk::class)->with('path', new PathIdentifier('foo'));

        $this->whenProject(Walk::class);
        $this->assertEquals($this->projection(Walk::class)->getCurrentStep(), null);
    }

    function withCurrentStep() {
        $this->given(Path::class, 'foo')->didPlanStep(new GoalIdentifier('one'), 1, false);
        $this->givenThat('DidChoosePath', Walk::class)->with('path', new PathIdentifier('foo'));
        $this->given(Path::class, 'foo')->didTakeNextStep();

        $this->whenProject(Walk::class);
        $this->assertEquals($this->projection(Walk::class)->getCurrentStep()->getGoal(), new GoalIdentifier('one'));
    }

    function hideNextStepWhileWalking() {
        $this->given(Path::class, 'foo')->didPlanStep(new GoalIdentifier('one'), 1, false);
        $this->given(Path::class, 'foo')->didPlanStep(new GoalIdentifier('two'), 1, false);
        $this->givenThat('DidChoosePath', Walk::class)->with('path', new PathIdentifier('foo'));
        $this->given(Path::class, 'foo')->didTakeNextStep();

        $this->whenProject(Walk::class);
        $this->assertEquals($this->projection(Walk::class)->getNextStep(), null);
    }

    function showUnitsLeft() {
        $this->given(Path::class, 'foo')->didPlanStep(new GoalIdentifier('one'), .5, false);
        $this->givenThat('DidChoosePath', Walk::class)->with('path', new PathIdentifier('foo'));
        $this->given(Path::class, 'foo')->didTakeNextStep();

        $this->whenProject(Walk::class);
        $this->assertEquals($this->projection(Walk::class)->getCurrentStep()->getUnitsLeft(), .5);
    }

    function calculateUnitsLeft() {
        $this->given(Path::class, 'foo')->didPlanStep(new GoalIdentifier('one'), .6, false);
        $this->givenThat('DidChoosePath', Walk::class)->with('path', new PathIdentifier('foo'));
        Time::freeze('12:00');
        $this->given(Path::class, 'foo')->didTakeNextStep();

        Time::freeze('12:10');
        $this->whenProject(Walk::class);
        $this->assertEquals($this->projection(Walk::class)->getCurrentStep()->getUnitsLeft(), .2);
    }

    function unitsLeftOfNotTakenStep() {
        $step = new Step(new GoalIdentifier('foo'), 2);
        $this->assertEquals($step->getUnitsLeft(), 2);

        $step->setCompleted(Time::now());
        $this->assertEquals($step->getUnitsLeft(), 0);
    }
}