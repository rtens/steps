<?php namespace spec\rtens\steps;

use rtens\steps\app\Application;
use rtens\steps\events\BlockFinished;
use rtens\steps\events\BlockPlanned;
use rtens\steps\model\BlockIdentifier;
use rtens\steps\model\GoalIdentifier;
use rtens\steps\model\Time;
use rtens\steps\projecting\Block;
use rtens\steps\projecting\Plan;
use rtens\steps\ShowPlan;
use watoki\karma\testing\Specification;

class ShowPlanSpec extends Specification {

    public function __construct() {
        parent::__construct(Application::sandbox());
    }

    public function singleBlock() {
        $this->given(new BlockPlanned(new BlockIdentifier('fooBlock'), new GoalIdentifier('foo'), 1, Time::now()));
        $this->when(new ShowPlan());
        $this->then->returnShouldMatch(function (Plan $plan) {
            return $plan->getBlocks() == [
                new Block(
                    new BlockIdentifier('fooBlock'), 1
                )
            ];
        });
    }

    public function finishedBlock() {
        $this->given(new BlockPlanned(new BlockIdentifier('fooBlock'), new GoalIdentifier('foo'), 1, Time::now()));
        $this->given(new BlockPlanned(new BlockIdentifier('barBlock'), new GoalIdentifier('bar'), 1, Time::now()));
        $this->given(new BlockFinished(new BlockIdentifier('fooBlock'), Time::now()));
        $this->when(new ShowPlan());

        $this->then->returnShouldMatch(function (Plan $plan) {
            return
                count($plan->getBlocks()) == 1
                && $plan->getBlocks()[0]->getBlock() == new BlockIdentifier('barBlock');
        });
    }

    public function sumUnits() {
        $this->given(new BlockPlanned(new BlockIdentifier('fooBlock'), new GoalIdentifier('foo'), .5, Time::now()));
        $this->given(new BlockPlanned(new BlockIdentifier('barBlock'), new GoalIdentifier('bar'), .8, Time::now()));
        $this->when(new ShowPlan());

        $this->then->returnShouldMatch(function (Plan $plan) {
            return $plan->getUnits() == 1.3;
        });
    }

    public function discardYesterday() {
        $this->given(new BlockPlanned(new BlockIdentifier('old'), new GoalIdentifier('foo'), 1, Time::at('yesterday')));
        $this->given(new BlockPlanned(new BlockIdentifier('new'), new GoalIdentifier('foo'), 1, Time::now()));
        $this->when(new ShowPlan());

        $this->then->returnShouldMatch(function (Plan $plan) {
            return $plan->getBlocks()[0]->getBlock() == new BlockIdentifier('new');
        });
    }
}