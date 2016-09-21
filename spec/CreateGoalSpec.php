<?php namespace spec\rtens\steps;

use rtens\steps\app\Application;
use rtens\steps\CreateGoal;
use rtens\steps\events\GoalCreated;
use watoki\karma\testing\Specification;

class CreateGoalSpec extends Specification {

    public function __construct() {
        parent::__construct(Application::builder());
    }

    function justName() {
        $this->when(new CreateGoal('Foo'));
        $this->then(new GoalCreated('Foo'));
    }
}