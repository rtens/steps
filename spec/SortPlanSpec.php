<?php namespace spec\rtens\steps;
use rtens\steps\app\Application;
use rtens\steps\events\BlockPlanned;
use rtens\steps\events\GoalCreated;
use rtens\steps\events\PlanSorted;
use rtens\steps\model\BlockIdentifier;
use rtens\steps\model\GoalIdentifier;
use rtens\steps\model\Time;
use rtens\steps\projecting\Plan;
use rtens\steps\ShowPlan;
use rtens\steps\SortPlan;
use watoki\karma\testing\Specification;

class SortPlanSpec extends Specification {

    public function __construct() {
        parent::__construct(Application::sandbox());
    }

    public function before() {
        $this->given(new GoalCreated(new GoalIdentifier('goal'), 'Goal', Time::now()));
    }

    public function success() {
        $this->when(new SortPlan([
            new BlockIdentifier('one'),
            new BlockIdentifier('two'),
            new BlockIdentifier('three')
        ]));
        $this->then(new PlanSorted([
            new BlockIdentifier('one'),
            new BlockIdentifier('two'),
            new BlockIdentifier('three')
        ], Time::now()));
    }

    public function sortedBeforeUnsorted() {
        $this->given(new BlockPlanned(new BlockIdentifier('foo'), new GoalIdentifier('goal'), 1, Time::now()));
        $this->given(new BlockPlanned(new BlockIdentifier('bar'), new GoalIdentifier('goal'), 1, Time::now()));
        $this->given(new BlockPlanned(new BlockIdentifier('baz'), new GoalIdentifier('goal'), 1, Time::now()));
        $this->when(new SortPlan([new BlockIdentifier('bar')]));
        $this->when(new ShowPlan());
        $this->then->returnShouldMatch(function (Plan $plan) {
            return $plan->getBlocks()[0]->getBlock() == new BlockIdentifier('bar');
        });
    }

    public function sortThree() {
        $this->given(new BlockPlanned(new BlockIdentifier('foo'), new GoalIdentifier('goal'), 1, Time::now()));
        $this->given(new BlockPlanned(new BlockIdentifier('bar'), new GoalIdentifier('goal'), 1, Time::now()));
        $this->given(new BlockPlanned(new BlockIdentifier('baz'), new GoalIdentifier('goal'), 1, Time::now()));
        $this->when(new SortPlan([
            new BlockIdentifier('bar'),
            new BlockIdentifier('baz'),
            new BlockIdentifier('foo'),
        ]));

        $this->when(new ShowPlan());
        $this->then->returnShouldMatch(function (Plan $plan) {
            return
                $plan->getBlocks()[0]->getBlock() == new BlockIdentifier('bar')
                && $plan->getBlocks()[1]->getBlock() == new BlockIdentifier('baz')
                && $plan->getBlocks()[2]->getBlock() == new BlockIdentifier('foo');
        });
    }
}