<?php
namespace rtens\steps2\domain;

use rtens\udity\check\DomainSpecification;
use rtens\udity\check\event\Events;

class AchieveOrGiveUpGoalSpec extends DomainSpecification {

    function achieveGoal() {
        $this->when(Goal::class)->doAchieve();
        $this->then(Events::named('DidAchieve'))->shouldBeAppended();
    }

    function cannotAchieveTwice() {
        $this->given(Goal::class)->didAchieve();
        $this->tryTo(Goal::class)->doAchieve();
        $this->thenShouldFailWith('Goal is already achieved');
    }

    function giveUpGoal() {
        $this->when(Goal::class)->doGiveUp();
        $this->then(Events::named('DidGiveUp'))->shouldBeAppended();
    }

    function cannotGiveUpTwice() {
        $this->given(Goal::class)->didGiveUp();
        $this->tryTo(Goal::class)->doGiveUp();
        $this->thenShouldFailWith('Goal was already given up');
    }

    function alreadyAchieved() {
        $this->given(Goal::class)->didAchieve();
        $this->tryTo(Goal::class)->doGiveUp();
        $this->thenShouldFailWith('Goal is already achieved');
    }

    function alreadyGivenUp() {
        $this->given(Goal::class)->didGiveUp();
        $this->tryTo(Goal::class)->doAchieve();
        $this->thenShouldFailWith('Goal was already given up');
    }
}