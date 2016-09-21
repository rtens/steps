<?php namespace spec\rtens\steps;

use rtens\steps\app\Application;
use rtens\steps\CreateGoal;
use rtens\steps\events\GoalCreated;
use rtens\steps\model\GoalIdentifier;
use rtens\steps\model\Time;
use watoki\karma\testing\Specification;

class CreateGoalSpec extends Specification {

    public function __construct() {
        parent::__construct(Application::sandbox());
    }

    function justName() {
        $this->when(new CreateGoal('Foo Bar'));
        $this->then(new GoalCreated(new GoalIdentifier('foobar'), 'Foo Bar', Time::now()));
    }
}