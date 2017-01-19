<?php
namespace rtens\steps2\domain\rank;

use rtens\steps2\domain\Goal;
use rtens\steps2\domain\GoalIdentifier;
use rtens\steps2\domain\GoalList;
use rtens\steps2\domain\Rating;
use rtens\udity\check\DomainSpecification;

class RankByRatingSpec extends DomainSpecification {

    function before() {
        $this->given(Goal::class, 'foo')->created('Foo');
    }

    function noRating() {
        $this->assertRank('foo', 0);
    }

    function minimumRating() {
        $this->given(Goal::class, 'foo')->didRate(new Rating(0, 0));
        $this->assertRank('foo', 0);
    }

    function maximumRating() {
        $this->given(Goal::class, 'foo')->didRate(new Rating(10, 10));
        $this->assertRank('foo', 30);
    }

    function noImportance() {
        $this->given(Goal::class, 'foo')->didRate(new Rating(0, 10));
        $this->assertRank('foo', 20);
    }

    function noUrgency() {
        $this->given(Goal::class, 'foo')->didRate(new Rating(10, 0));
        $this->assertRank('foo', 10);
    }

    function inheritRating() {
        $this->given(Goal::class, 'foo')->didRate(new Rating(6, 7));

        $this->given(Goal::class, 'bar')->created('Bar');
        $this->given(Goal::class, 'bar')->doMove(new GoalIdentifier('foo'));

        $this->assertRank('bar', 20);
    }

    private function assertRank($key, $expected) {
        $goals = $this->whenProject(GoalList::class)->getGoals();
        $this->assertEquals($goals[$key]->getRank(), $expected);
    }
}