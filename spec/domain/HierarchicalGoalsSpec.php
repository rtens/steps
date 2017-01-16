<?php
namespace rtens\steps2\domain;

use rtens\udity\check\DomainSpecification;
use rtens\udity\check\event\Events;

class HierarchicalGoalsSpec extends DomainSpecification {

    function moveGoal() {
        $this->when(Goal::class)->doMove(new GoalIdentifier('foo'));
        $this->then(Events::named('DidMove')->with('parent', new GoalIdentifier('foo')));
    }

    function makeOrphan() {
        $this->when(Goal::class)->doMove(null);
        $this->then(Events::named('DidMove')->with('parent', null));
    }

    function cannotBeItsOwnParent() {
        $this->tryTo(Goal::class, 'foo')->doMove(new GoalIdentifier('foo'));
        $this->thenShouldFailWith('Goal cannot be its own parent');
    }
}