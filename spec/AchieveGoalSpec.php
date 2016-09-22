<?php namespace spec\rtens\steps;
use rtens\steps\AchieveGoal;
use rtens\steps\app\Application;
use rtens\steps\events\GoalAchieved;
use rtens\steps\model\GoalIdentifier;
use rtens\steps\model\Time;
use watoki\karma\testing\Specification;

class AchieveGoalSpec extends Specification {

    public function __construct() {
        parent::__construct(Application::sandbox());
    }

    public function now() {
        $this->when(new AchieveGoal(new GoalIdentifier('foo')));
        $this->then(new GoalAchieved(new GoalIdentifier('foo'), Time::now()));
    }

    public function before(){
        $this->when(new AchieveGoal(new GoalIdentifier('foo'), new \DateTime('2011-12-13')));
        $this->then(new GoalAchieved(new GoalIdentifier('foo'), new \DateTime('2011-12-13')));
    }
}