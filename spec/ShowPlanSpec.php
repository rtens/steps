<?php namespace spec\rtens\steps;

use rtens\steps\app\Application;
use rtens\steps\events\BlockCancelled;
use rtens\steps\events\BlockFinished;
use rtens\steps\events\BlockPlanned;
use rtens\steps\events\GoalCreated;
use rtens\steps\model\BlockIdentifier;
use rtens\steps\model\GoalIdentifier;
use rtens\steps\model\Time;
use rtens\steps\projecting\Plan;
use rtens\steps\ShowPlan;
use watoki\karma\testing\Specification;

class ShowPlanSpec extends Specification {

    public function __construct() {
        parent::__construct(Application::sandbox());
    }

    public function before() {
        $this->given(new GoalCreated(new GoalIdentifier('goal'), 'Foo', Time::now()));
    }

    public function singleBlock() {
        $this->given(new BlockPlanned(new BlockIdentifier('foo'), new GoalIdentifier('goal'), 1, Time::now()));
        $this->when(new ShowPlan());
        $this->then->returnShouldMatch(function (Plan $plan) {
            $block = $plan->getBlocks()[0];
            return
                $block->getBlock() == new BlockIdentifier('foo')
                && $block->getUnits() == 1;
        });
    }

    public function finishedBlock() {
        $this->given(new BlockPlanned(new BlockIdentifier('foo'), new GoalIdentifier('goal'), 1, Time::now()));
        $this->given(new BlockPlanned(new BlockIdentifier('bar'), new GoalIdentifier('goal'), 1, Time::now()));
        $this->given(new BlockFinished(new BlockIdentifier('foo'), Time::now()));
        $this->when(new ShowPlan());

        $this->then->returnShouldMatch(function (Plan $plan) {
            return
                count($plan->getBlocks()) == 1
                && $plan->getBlocks()[0]->getBlock() == new BlockIdentifier('bar');
        });
    }

    public function sumUnits() {
        $this->given(new BlockPlanned(new BlockIdentifier('foo'), new GoalIdentifier('goal'), .5, Time::now()));
        $this->given(new BlockPlanned(new BlockIdentifier('bar'), new GoalIdentifier('goal'), .8, Time::now()));
        $this->when(new ShowPlan());

        $this->then->returnShouldMatch(function (Plan $plan) {
            return $plan->getUnits() == 1.3;
        });
    }

    public function discardYesterday() {
        $this->given(new BlockPlanned(new BlockIdentifier('old'), new GoalIdentifier('goal'), 1, Time::at('yesterday')));
        $this->given(new BlockPlanned(new BlockIdentifier('new'), new GoalIdentifier('goal'), 1, Time::now()));
        $this->when(new ShowPlan());

        $this->then->returnShouldMatch(function (Plan $plan) {
            return $plan->getBlocks()[0]->getBlock() == new BlockIdentifier('new');
        });
    }

    public function hideCancelledBlocks() {
        $this->given(new BlockPlanned(new BlockIdentifier('foo'), new GoalIdentifier('goal'), 1, Time::now()));
        $this->given(new BlockPlanned(new BlockIdentifier('bar'), new GoalIdentifier('goal'), 1, Time::now()));
        $this->given(new BlockCancelled(new BlockIdentifier('foo'), Time::now()));
        $this->when(new ShowPlan());

        $this->then->returnShouldMatch(function (Plan $plan) {
            return
                count($plan->getBlocks()) == 1
                && $plan->getBlocks()[0]->getBlock() == new BlockIdentifier('bar');
        });
    }
}