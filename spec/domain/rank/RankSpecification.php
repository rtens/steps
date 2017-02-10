<?php
namespace rtens\steps2\domain\rank;

use rtens\steps2\domain\Goal;
use rtens\udity\check\DomainSpecification;
use rtens\steps2\domain\GoalList;

abstract class RankSpecification extends DomainSpecification {

    function before() {
        $this->given(Goal::class, 'foo')->created('Foo');
    }

    protected function assertRank($expected, $goal = 'foo') {
        $goals = $this->whenProject(GoalList::class)->getGoals();
        $this->assertEquals($goals[$goal]->getRank(), $expected);
    }
}