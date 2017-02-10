<?php
namespace rtens\steps2\domain\rank;

use rtens\steps2\domain\Goal;
use rtens\steps2\domain\GoalIdentifier;
use rtens\steps2\FakeEvent;
use rtens\udity\utils\Time;

class RankByDeadlineSpec extends RankSpecification {

    function noDeadline() {
        $this->assertRank(0);
    }

    function farAwayDeadline() {
        $this->given(Goal::class, 'foo')->setDeadline(Time::at('37 days'));
        $this->assertRank(0);
    }

    function soonDeadline() {
        $this->given(Goal::class, 'foo')->setDeadline(Time::at('7 days'));
        $this->assertRank(30);
    }

    function missedDeadline() {
        $this->given(Goal::class, 'foo')->setDeadline(Time::at('1 day ago'));
        $this->assertRank(30);
    }

    function inheritDeadline() {
        $this->given(Goal::class, 'bar')->created('Bar', new FakeEvent());
        $this->given(Goal::class, 'bar')->setDeadline(Time::at('22 days'));

        $this->given(Goal::class, 'foo')->doMove(new GoalIdentifier('bar'));

        $this->assertRank(15);
    }
}