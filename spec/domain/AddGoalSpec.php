<?php
namespace rtens\steps2\domain;

use rtens\udity\check\DomainSpecification;
use rtens\udity\check\event\Events;
use rtens\udity\Event;

class AddGoalSpec extends DomainSpecification {

    function withName() {
        $this->when(Goal::class)->created('Foo');
        $this->then(Events::named('Created'))->should(function (Event $event) {
            return $event->getAggregateIdentifier() instanceof GoalIdentifier;
        });
    }
}