<?php namespace spec\rtens\steps;
use rtens\steps\app\Application;
use rtens\steps\events\DeadlineSet;
use rtens\steps\model\GoalIdentifier;
use rtens\steps\model\Time;
use rtens\steps\SetDeadline;
use watoki\karma\testing\Specification;

class SetDeadlineSpec extends Specification {

    public function __construct() {
        parent::__construct(Application::sandbox());
    }

    public function success() {
        $this->when(new SetDeadline(new GoalIdentifier('foo'), new \DateTime('2011-12-13 14:15')));
        $this->then(new DeadlineSet(new GoalIdentifier('foo'), new \DateTime('2011-12-13 14:15'), Time::now()));
    }
}