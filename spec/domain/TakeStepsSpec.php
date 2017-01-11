<?php
namespace rtens\steps2\domain;

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
        $this->given(Path::class)->didTakeNextStep();
        $this->tryTo(Path::class)->doTakeNextStep();
        $this->thenShouldFailWith('Already taking a step');
    }

    function noStepToComplete() {
        $this->tryTo(Path::class)->doCompleteStep();
        $this->thenShouldFailWith('Not taking any step');
    }

    function completeStep() {
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('myGoal'), 1, false);
        $this->given(Path::class)->didTakeNextStep();
        $this->when(Path::class)->doCompleteStep();
        $this->then(Events::named('DidCompleteStep'))->shouldBeAppended();
    }

    function completedStep() {
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('myGoal'), 1, false);
        $this->given(Path::class)->didTakeNextStep();
        $this->given(Path::class)->didCompleteStep();

        $this->tryTo(Path::class)->doCompleteStep();
        $this->thenShouldFailWith('Not taking any step');
    }

    function projectNextSteps() {
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('First'), 1, false);
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('Second'), 1, false);

        $this->whenProjectObject(Path::class)
            ->assertEquals(count($this->projection(Path::class)->getCompletedSteps()), 0)
            ->assertEquals(count($this->projection(Path::class)->getRemainingSteps()), 2)
            ->assertEquals($this->projection(Path::class)->getRemainingSteps()[0]->getGoal(), new GoalIdentifier('First'))
            ->assertEquals($this->projection(Path::class)->getRemainingSteps()[1]->getGoal(), new GoalIdentifier('Second'))
            ->assertEquals($this->projection(Path::class)->getCurrentStep(), null);
    }

    function projectCurrentStep() {
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('First'), 1, false);
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('Second'), 1, false);
        $this->given(Path::class)->didTakeNextStep();

        $this->whenProjectObject(Path::class)
            ->assertEquals(count($this->projection(Path::class)->getCompletedSteps()), 0)
            ->assertEquals(count($this->projection(Path::class)->getRemainingSteps()), 1)
            ->assertEquals($this->projection(Path::class)->getRemainingSteps()[0]->getGoal(), new GoalIdentifier('Second'))
            ->assertEquals($this->projection(Path::class)->getCurrentStep()->getStarted(), Time::now())
            ->assertEquals($this->projection(Path::class)->getCurrentStep()->getGoal(), new GoalIdentifier('First'));
    }

    function takeSecondStep() {
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('First'), 1, false);
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('Second'), 1, false);
        $this->given(Path::class)->didTakeNextStep();
        $this->given(Path::class)->didCompleteStep();

        $this->whenProjectObject(Path::class)
            ->assertEquals(count($this->projection(Path::class)->getCompletedSteps()), 1)
            ->assertEquals($this->projection(Path::class)->getCompletedSteps()[0]->getGoal(), new GoalIdentifier('First'))
            ->assertEquals($this->projection(Path::class)->getCompletedSteps()[0]->getCompleted(), Time::now())
            ->assertEquals(count($this->projection(Path::class)->getRemainingSteps()), 1)
            ->assertEquals($this->projection(Path::class)->getRemainingSteps()[0]->getGoal(), new GoalIdentifier('Second'));
    }
}