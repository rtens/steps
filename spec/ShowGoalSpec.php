<?php namespace spec\rtens\steps;
use rtens\steps\app\Application;
use rtens\steps\events\GoalCreated;
use rtens\steps\model\GoalIdentifier;
use rtens\steps\model\Time;
use rtens\steps\projecting\Goal;
use rtens\steps\ShowGoal;
use watoki\karma\testing\Specification;

class ShowGoalSpec extends Specification {

    public function __construct() {
        parent::__construct(Application::sandbox());
    }

    public function success() {
        $this->given(new GoalCreated(new GoalIdentifier('foo'), 'Foo', Time::now()));
        $this->when(new ShowGoal(new GoalIdentifier('foo')));
        $this->then->returnShouldMatch(function (Goal $goal) {
            return
                $goal->getGoal() == new GoalIdentifier('foo')
                && $goal->getName() == 'Foo';
        });
    }
}