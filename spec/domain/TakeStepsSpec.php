<?php
namespace rtens\steps2\domain;

use rtens\udity\check\DomainSpecification;
use rtens\udity\check\event\Events;
use rtens\udity\utils\Time;

class TakeStepsSpec extends DomainSpecification {

    function noSteps() {
        $this->tryTo(Plan::class)->doStartNextStep();
        $this->thenShouldFailWith('No next step to start');
    }

    function takeNextStep() {
        $this->given(Plan::class)->didPlanStep(new GoalIdentifier('MyGoal'), 1, false);
        $this->when(Plan::class)->doStartNextStep();
        $this->then(Events::named('DidStartNextStep'))->shouldBeAppended();
    }

    function alreadyTakingStep() {
        $this->given(Plan::class)->didPlanStep(new GoalIdentifier('MyGoal'), 1, false);
        $this->given(Plan::class)->didStartNextStep();
        $this->tryTo(Plan::class)->doStartNextStep();
        $this->thenShouldFailWith('Already taking a step');
    }

    function noStepToComplete() {
        $this->tryTo(Plan::class)->doCompleteStep();
        $this->thenShouldFailWith('Not taking any step');
    }

    function completeStep() {
        $this->given(Plan::class)->didPlanStep(new GoalIdentifier('myGoal'), 1, false);
        $this->given(Plan::class)->didStartNextStep();
        $this->when(Plan::class)->doCompleteStep();
        $this->then(Events::named('DidCompleteStep'))->shouldBeAppended();
    }

    function completedStep() {
        $this->given(Plan::class)->didPlanStep(new GoalIdentifier('myGoal'), 1, false);
        $this->given(Plan::class)->didStartNextStep();
        $this->given(Plan::class)->didCompleteStep();

        $this->tryTo(Plan::class)->doCompleteStep();
        $this->thenShouldFailWith('Not taking any step');
    }

    function projectNextSteps() {
        $this->given(Plan::class)->didPlanStep(new GoalIdentifier('First'), 1, false);
        $this->given(Plan::class)->didPlanStep(new GoalIdentifier('Second'), 1, false);

        $this->whenProjectObject(Plan::class)
            ->assertEquals(count($this->projection(Plan::class)->getCompletedSteps()), 0)
            ->assertEquals(count($this->projection(Plan::class)->getNextSteps()), 2)
            ->assertEquals($this->projection(Plan::class)->getNextSteps()[0]->getGoal(), new GoalIdentifier('First'))
            ->assertEquals($this->projection(Plan::class)->getNextSteps()[1]->getGoal(), new GoalIdentifier('Second'))
            ->assertEquals($this->projection(Plan::class)->getCurrentStep(), null);
    }

    function projectCurrentStep() {
        $this->given(Plan::class)->didPlanStep(new GoalIdentifier('First'), 1, false);
        $this->given(Plan::class)->didPlanStep(new GoalIdentifier('Second'), 1, false);
        $this->given(Plan::class)->didStartNextStep();

        $this->whenProjectObject(Plan::class)
            ->assertEquals(count($this->projection(Plan::class)->getCompletedSteps()), 0)
            ->assertEquals(count($this->projection(Plan::class)->getNextSteps()), 1)
            ->assertEquals($this->projection(Plan::class)->getNextSteps()[0]->getGoal(), new GoalIdentifier('Second'))
            ->assertEquals($this->projection(Plan::class)->getCurrentStep()->getStarted(), Time::now())
            ->assertEquals($this->projection(Plan::class)->getCurrentStep()->getGoal(), new GoalIdentifier('First'));
    }

    function takeSecondStep() {
        $this->given(Plan::class)->didPlanStep(new GoalIdentifier('First'), 1, false);
        $this->given(Plan::class)->didPlanStep(new GoalIdentifier('Second'), 1, false);
        $this->given(Plan::class)->didStartNextStep();
        $this->given(Plan::class)->didCompleteStep();

        $this->whenProjectObject(Plan::class)
            ->assertEquals(count($this->projection(Plan::class)->getCompletedSteps()), 1)
            ->assertEquals($this->projection(Plan::class)->getCompletedSteps()[0]->getGoal(), new GoalIdentifier('First'))
            ->assertEquals($this->projection(Plan::class)->getCompletedSteps()[0]->getCompleted(), Time::now())
            ->assertEquals(count($this->projection(Plan::class)->getNextSteps()), 1)
            ->assertEquals($this->projection(Plan::class)->getNextSteps()[0]->getGoal(), new GoalIdentifier('Second'));
    }
}