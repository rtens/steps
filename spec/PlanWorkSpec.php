<?php namespace spec\rtens\steps;

use rtens\steps\app\Application;
use rtens\steps\events\GoalCreated;
use rtens\steps\events\WorkPlanned;
use rtens\steps\model\GoalIdentifier;
use rtens\steps\model\Time;
use rtens\steps\PlanWork;
use watoki\karma\testing\Specification;

class PlanWorkSpec extends Specification {

    public function __construct() {
        parent::__construct(Application::sandbox());
    }

    public function defaultLength() {
        $this->given(new GoalCreated(new GoalIdentifier('foo'), 'Foo', Time::now()));
        $this->when(new PlanWork(new GoalIdentifier('foo')));
        $this->then(new WorkPlanned(new GoalIdentifier('foo'), 1, Time::now()));
    }
}