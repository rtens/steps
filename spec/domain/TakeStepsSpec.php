<?php
namespace rtens\steps2\domain;

use rtens\steps2\FakeEvent;
use rtens\udity\check\DomainSpecification;
use rtens\udity\check\event\Events;
use rtens\udity\utils\Time;

class TakeStepsSpec extends DomainSpecification {

    function noSteps() {
        $this->tryTo(Path::class)->doTakeNextStep();
        $this->thenShouldFailWith('No next step to start');
    }

    function takeNextStep() {
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('MyGoal'), 1, false);
        $this->when(Path::class)->doTakeNextStep();
        $this->then(Events::named('DidTakeNextStep'))->shouldBeAppended();
    }

    function alreadyTakingStep() {
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('MyGoal'), 1, false);
        $this->given(Path::class)->didTakeNextStep(new FakeEvent());
        $this->tryTo(Path::class)->doTakeNextStep();
        $this->thenShouldFailWith('Already taking a step');
    }

    function noStepToComplete() {
        $this->tryTo(Path::class)->doCompleteStep();
        $this->thenShouldFailWith('Not taking any step');
    }

    function completeStep() {
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('myGoal'), 1, false);
        $this->given(Path::class)->didTakeNextStep(new FakeEvent());
        $this->when(Path::class)->doCompleteStep();
        $this->then(Events::named('DidCompleteStep'))->shouldBeAppended();
    }

    function stepAlreadyCompleted() {
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('myGoal'), 1, false);
        $this->given(Path::class)->didTakeNextStep(new FakeEvent());
        $this->given(Path::class)->didCompleteStep(new FakeEvent());

        $this->tryTo(Path::class)->doCompleteStep();
        $this->thenShouldFailWith('Not taking any step');
    }

    function skipStep() {
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('MyGoal'), 1, false);

        $this->when(Path::class)->doSkipNextStep();
        $this->then(Events::named('DidSkipNextStep'))->shouldBeAppended();
    }

    function noMoreStepsLeftToSkip() {
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('MyGoal'), 1, false);
        $this->given(Path::class)->didTakeNextStep(new FakeEvent());
        $this->given(Path::class)->didCompleteStep(new FakeEvent());

        $this->tryTo(Path::class)->doSkipNextStep();
        $this->thenShouldFailWith('No next step to skip');
    }

    function projectNextSteps() {
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('First'), 1, false);
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('Second'), 1, false);

        $path = $this->whenProjectObject(Path::class);

        $this->assertEquals(count($path->getCompletedSteps()), 0);
        $this->assertEquals(count($path->getRemainingSteps()), 2);
        $this->assertEquals($path->getRemainingSteps()[0]->getGoal(), new GoalIdentifier('First'));
        $this->assertEquals($path->getRemainingSteps()[1]->getGoal(), new GoalIdentifier('Second'));
        $this->assertEquals($path->getCurrentStep(), null);
    }

    function projectCurrentStep() {
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('First'), 1, false);
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('Second'), 1, false);
        $this->given(Path::class)->didTakeNextStep(new FakeEvent());

        $path = $this->whenProjectObject(Path::class);

        $this->assertEquals(count($path->getCompletedSteps()), 0);
        $this->assertEquals(count($path->getRemainingSteps()), 1);
        $this->assertEquals($path->getRemainingSteps()[0]->getGoal(), new GoalIdentifier('Second'));
        $this->assertEquals($path->getCurrentStep()->getStarted(), Time::now());
        $this->assertEquals($path->getCurrentStep()->getGoal(), new GoalIdentifier('First'));
    }

    function completeFirstStep() {
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('First'), 1, false);
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('Second'), 1, false);
        $this->given(Path::class)->didTakeNextStep(new FakeEvent());
        $this->given(Path::class)->didCompleteStep(new FakeEvent());

        $this->whenProjectObject(Path::class);
        $this->assertEquals(count($this->projection(Path::class)->getCompletedSteps()), 1);
        $this->assertEquals($this->projection(Path::class)->getCompletedSteps()[0]->getGoal(), new GoalIdentifier('First'));
        $this->assertEquals($this->projection(Path::class)->getCompletedSteps()[0]->getCompleted(), Time::now());
        $this->assertEquals(count($this->projection(Path::class)->getRemainingSteps()), 1);
        $this->assertEquals($this->projection(Path::class)->getRemainingSteps()[0]->getGoal(), new GoalIdentifier('Second'));
    }

    function skipSecondStep() {
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('First'), 1, false);
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('Second'), 1, false);
        $this->given(Path::class)->didTakeNextStep(new FakeEvent());
        $this->given(Path::class)->didCompleteStep(new FakeEvent());
        $this->given(Path::class)->didSkipNextStep();

        $this->whenProjectObject(Path::class);
        $this->assertEquals(count($this->projection(Path::class)->getCompletedSteps()), 1);
        $this->assertEquals($this->projection(Path::class)->getCompletedSteps()[0]->getGoal(), new GoalIdentifier('First'));
        $this->assertEquals(count($this->projection(Path::class)->getRemainingSteps()), 0);
    }

    function notAPlannedStep() {
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('First'), 1, false);
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('Second'), 1, false);

        $this->tryTo(Path::class)->doTakeStep(new GoalIdentifier('foo'));
        $this->thenShouldFailWith('No remaining step for this goal');
    }

    function stepOfGoalAlreadyCompleted() {
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('First'), 1, false);
        $this->given(Path::class)->didTakeNextStep(new FakeEvent());
        $this->given(Path::class)->didCompleteStep(new FakeEvent());

        $this->tryTo(Path::class)->doTakeStep(new GoalIdentifier('First'));
        $this->thenShouldFailWith('No remaining step for this goal');
    }

    function takeAnyStep() {
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('Second'), 1, false);
        $this->given(Path::class)->didTakeNextStep(new FakeEvent());
        $this->given(Path::class)->didCompleteStep(new FakeEvent());

        $this->given(Path::class)->didPlanStep(new GoalIdentifier('First'), 1, false);
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('Second'), 1, false);
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('Second'), 1, false);
        $this->given(Path::class)->didTakeStep(new GoalIdentifier('Second'), new FakeEvent());

        $this->whenProjectObject(Path::class);
        $this->assertEquals($this->projection(Path::class)->getCurrentStep()->getGoal(), new GoalIdentifier('Second'));
        $this->assertEquals(count($this->projection(Path::class)->getRemainingSteps()), 2);
        $this->assertEquals($this->projection(Path::class)->getRemainingSteps()[0]->getGoal(), new GoalIdentifier('First'));
        $this->assertEquals($this->projection(Path::class)->getRemainingSteps()[1]->getGoal(), new GoalIdentifier('Second'));
    }
}