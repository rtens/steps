<?php namespace spec\rtens\steps;

use rtens\steps\app\Application;
use rtens\steps\events\GoalCreated;
use rtens\steps\ListGoals;
use rtens\steps\model\Goal;
use rtens\steps\projecting\GoalList;
use watoki\karma\testing\Specification;

class ListGoalsSpec extends Specification {

    public function __construct() {
        parent::__construct(Application::builder());
    }

    public function withJustNames() {
        $this->given(new GoalCreated('Foo'));
        $this->when(new ListGoals());
        $this->then->returnShouldMatch(function (GoalList $list) {
            return $list->getGoals() == [
                new Goal('Foo')
            ];
        });
    }
}