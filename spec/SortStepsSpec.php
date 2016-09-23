<?php namespace spec\rtens\steps;

use rtens\steps\app\Application;
use rtens\steps\events\GoalCreated;
use rtens\steps\events\StepAdded;
use rtens\steps\events\StepsSorted;
use rtens\steps\model\GoalIdentifier;
use rtens\steps\model\StepIdentifier;
use rtens\steps\model\Time;
use rtens\steps\projecting\Goal;
use rtens\steps\ShowGoal;
use rtens\steps\SortSteps;
use watoki\karma\testing\Specification;

class SortStepsSpec extends Specification {

    public function __construct() {
        parent::__construct(Application::sandbox());
    }

    public function success() {
        $this->when(new SortSteps(new GoalIdentifier('foo'), [
            new StepIdentifier('one'),
            new StepIdentifier('two')
        ]));
        $this->then(new StepsSorted(new GoalIdentifier('foo'), [
            new StepIdentifier('one'),
            new StepIdentifier('two')
        ], Time::now()));
    }

    public function useSorting() {
        $this->given(new GoalCreated(new GoalIdentifier('goal'), 'Goal', Time::now()));
        $this->given(new StepAdded(new StepIdentifier('loo'), new GoalIdentifier('goal'), 'Foo'));
        $this->given(new StepAdded(new StepIdentifier('foo'), new GoalIdentifier('goal'), 'Foo'));
        $this->given(new StepAdded(new StepIdentifier('bar'), new GoalIdentifier('goal'), 'Bar'));
        $this->given(new StepAdded(new StepIdentifier('baz'), new GoalIdentifier('goal'), 'Baz'));

        $this->given(new StepsSorted(new GoalIdentifier('goal'), [
            new StepIdentifier('bar'),
            new StepIdentifier('baz'),
            new StepIdentifier('foo')
        ], Time::now()));

        $this->when(new ShowGoal(new GoalIdentifier('goal')));
        $this->then->returnShouldMatch(function (Goal $goal) {
            return
                $goal->getSteps()[0]->getStep() == new StepIdentifier('bar')
                && $goal->getSteps()[1]->getStep() == new StepIdentifier('baz')
                && $goal->getSteps()[2]->getStep() == new StepIdentifier('foo')
                && $goal->getSteps()[3]->getStep() == new StepIdentifier('loo');
        });
    }
}