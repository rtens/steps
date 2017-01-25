<?php
namespace rtens\steps2\domain\rank;

use rtens\steps2\domain\Goal;
use rtens\steps2\domain\GoalIdentifier;
use rtens\steps2\domain\Rating;

class RankByRatingSpec extends RankSpecification {

    function noRating() {
        $this->assertRank(0);
    }

    function minimumRating() {
        $this->given(Goal::class, 'foo')->didRate(new Rating(0, 0));
        $this->assertRank(0);
    }

    function maximumRating() {
        $this->given(Goal::class, 'foo')->didRate(new Rating(10, 10));
        $this->assertRank(30);
    }

    function noImportance() {
        $this->given(Goal::class, 'foo')->didRate(new Rating(0, 10));
        $this->assertRank(20);
    }

    function noUrgency() {
        $this->given(Goal::class, 'foo')->didRate(new Rating(10, 0));
        $this->assertRank(10);
    }

    function inheritRating() {
        $this->given(Goal::class, 'bar')->created('Bar');
        $this->given(Goal::class, 'bar')->didRate(new Rating(6, 7));

        $this->given(Goal::class, 'foo')->doMove(new GoalIdentifier('bar'));

        $this->assertRank(20);
    }
}