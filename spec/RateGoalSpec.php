<?php namespace spec\rtens\steps;

use rtens\steps\app\Application;
use rtens\steps\events\GoalRated;
use rtens\steps\model\GoalIdentifier;
use rtens\steps\model\Time;
use rtens\steps\RateGoal;
use watoki\karma\testing\Specification;

class RateGoalSpec extends Specification {

    public function __construct() {
        parent::__construct(Application::sandbox());
    }

    public function success() {
        $this->when(new RateGoal(new GoalIdentifier('foo'), 1, 1));
        $this->then(new GoalRated(new GoalIdentifier('foo'), 1, 1, Time::now()));
    }
}