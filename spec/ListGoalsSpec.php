<?php namespace spec\rtens\steps;

use rtens\steps\app\Application;
use rtens\steps\events\GoalCreated;
use rtens\steps\events\StepAdded;
use rtens\steps\events\StepCompleted;
use rtens\steps\ListGoals;
use rtens\steps\model\Goal;
use rtens\steps\model\GoalIdentifier;
use rtens\steps\model\StepIdentifier;
use rtens\steps\model\Time;
use rtens\steps\projecting\GoalList;
use watoki\karma\testing\Specification;

class ListGoalsSpec extends Specification {

    public function __construct() {
        parent::__construct(Application::sandbox());
    }

    public function withJustNames() {
        $this->given(new GoalCreated(new GoalIdentifier('foo'), 'Foo', Time::now()));
        $this->when(new ListGoals());
        $this->then->returnShouldMatch(function (GoalList $list) {
            return $list->getGoals() == [
                new Goal(new GoalIdentifier('foo'), 'Foo')
            ];
        });
    }

    public function withSteps() {
        $this->given(new GoalCreated(new GoalIdentifier('foo'), 'Foo', Time::now()));
        $this->given(new StepAdded(new StepIdentifier('foo_one'), new GoalIdentifier('foo'), 'one'));
        $this->when(new ListGoals());
        $this->then->returnShouldMatch(function (GoalList $list) {
            return $list->getGoals() == [
                new Goal(new GoalIdentifier('foo'), 'Foo', [
                    new StepIdentifier('foo_one')
                ])
            ];
        });
    }

    public function hideCompletedSteps() {
        $this->given(new GoalCreated(new GoalIdentifier('foo'), 'Foo', Time::now()));
        $this->given(new StepAdded(new StepIdentifier('foo_one'), new GoalIdentifier('foo'), 'one'));
        $this->given(new StepAdded(new StepIdentifier('foo_two'), new GoalIdentifier('foo'), 'two'));
        $this->given(new StepCompleted(new StepIdentifier('foo_one'), Time::now()));
        $this->when(new ListGoals());

        $this->then->returnShouldMatch(function (GoalList $list) {
            return $list->getGoals() == [
                new Goal(new GoalIdentifier('foo'), 'Foo', [
                    new StepIdentifier('foo_two')
                ])
            ];
        });
    }
}