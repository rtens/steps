<?php namespace spec\rtens\steps;
use rtens\steps\app\Application;
use rtens\steps\events\GoalCreated;
use rtens\steps\events\BlockPlanned;
use rtens\steps\model\BlockIdentifier;
use rtens\steps\model\GoalIdentifier;
use rtens\steps\model\Time;
use rtens\steps\AddGoalToPlan;
use watoki\karma\testing\Specification;

class AddGoalToPlanSpec extends Specification {

    public function __construct() {
        parent::__construct(Application::sandbox());
        Time::freeze(new \DateTime('2011-12-13'));
    }

    public function defaultLength() {
        $this->given(new GoalCreated(new GoalIdentifier('foo'), 'Foo', Time::now()));
        $this->when(new AddGoalToPlan(new GoalIdentifier('foo')));
        $this->then(new BlockPlanned(new BlockIdentifier('foo_20111213_1'), new GoalIdentifier('foo'), 1, Time::now()));
    }

    public function lessThanAUnit() {
        $this->given(new GoalCreated(new GoalIdentifier('foo'), 'Foo', Time::now()));
        $this->when(new AddGoalToPlan(new GoalIdentifier('foo'), .1));
        $this->then(new BlockPlanned(new BlockIdentifier('foo_20111213_1'), new GoalIdentifier('foo'), .1, Time::now()));
    }

    public function moreThanAUnit() {
        $this->given(new GoalCreated(new GoalIdentifier('foo'), 'Foo', Time::now()));
        $this->when(new AddGoalToPlan(new GoalIdentifier('foo'), 2.25));

        $this->then(new BlockPlanned(new BlockIdentifier('foo_20111213_1'), new GoalIdentifier('foo'), 1, Time::now()));
        $this->then(new BlockPlanned(new BlockIdentifier('foo_20111213_2'), new GoalIdentifier('foo'), 1, Time::now()));
        $this->then(new BlockPlanned(new BlockIdentifier('foo_20111213_3'), new GoalIdentifier('foo'), .25, Time::now()));
    }
}