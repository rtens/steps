<?php
namespace rtens\steps2\domain\rank;

use rtens\steps2\domain\Goal;
use rtens\udity\check\DomainSpecification;
use rtens\steps2\domain\GoalList;

abstract class RankSpecification extends DomainSpecification {

    function before() {
        $this->given(Goal::class)->created('Foo');
    }

    protected function assertRank($expected) {
        $goals = $this->whenProject(GoalList::class)->getGoals();
        $this->assertEquals($goals[self::DEFAULT_KEY]->getRank(), $expected);
    }
}