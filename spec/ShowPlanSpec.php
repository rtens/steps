<?php namespace spec\rtens\steps;

use rtens\steps\app\Application;
use rtens\steps\events\BlockFinished;
use rtens\steps\events\BlockPlanned;
use rtens\steps\model\Block;
use rtens\steps\model\BlockIdentifier;
use rtens\steps\model\GoalIdentifier;
use rtens\steps\model\Plan;
use rtens\steps\model\Time;
use rtens\steps\ShowPlan;
use watoki\karma\testing\Specification;

;

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
                    new BlockIdentifier('fooBlock'),
                    new GoalIdentifier('foo'),
                    1
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
}