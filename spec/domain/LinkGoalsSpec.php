<?php
namespace rtens\steps2\domain;

use rtens\udity\check\DomainSpecification;
use rtens\udity\check\event\Events;

class LinkGoalsSpec extends DomainSpecification {

    function linkGoal() {
        $this->when(Goal::class)->doLink(new GoalIdentifier('foo'));
        $this->then(Events::named('DidLink')->with('to', new GoalIdentifier('foo')));
    }

    function cannotLinkToSelf() {
        $this->tryTo(Goal::class, 'foo')->doLink(new GoalIdentifier('foo'));
        $this->thenShouldFailWith('Cannot link a Goal to itself');
    }

    function hideGoalsWithOpenLinks() {
        $this->given(Goal::class, 'foo')->created('Foo');
        $this->given(Goal::class, 'bar')->created('Bar');
        $this->given(Goal::class, 'bar')->doLink(new GoalIdentifier('foo'));

        $this->whenProject(GoalList::class);

        $list = $this->projection(GoalList::class)->getList();
        $this->assertEquals(count($list), 1);
        $this->assertEquals($list[0]->getName(), 'Foo');
    }

    function hideGoalWithAnyOpenLink() {
        $this->given(Goal::class, 'foo')->created('Foo');
        $this->given(Goal::class, 'bar')->created('Bar');
        $this->given(Goal::class, 'baz')->created('Baz');

        $this->given(Goal::class, 'baz')->doLink(new GoalIdentifier('foo'));
        $this->given(Goal::class, 'baz')->doLink(new GoalIdentifier('bar'));

        $this->given(Goal::class, 'foo')->didAchieve();

        $this->whenProject(GoalList::class);

        $list = $this->projection(GoalList::class)->getList();
        $this->assertEquals(count($list), 1);
        $this->assertEquals($list[0]->getName(), 'Bar');
    }

    function showGoalsWithAchievedLinks() {
        $this->given(Goal::class, 'foo')->created('Foo');
        $this->given(Goal::class, 'bar')->created('Bar');
        $this->given(Goal::class, 'baz')->created('Baz');

        $this->given(Goal::class, 'baz')->doLink(new GoalIdentifier('foo'));
        $this->given(Goal::class, 'baz')->doLink(new GoalIdentifier('bar'));

        $this->given(Goal::class, 'foo')->didAchieve();
        $this->given(Goal::class, 'bar')->didAchieve();

        $this->whenProject(GoalList::class);

        $list = $this->projection(GoalList::class)->getList();
        $this->assertEquals(count($list), 1);
        $this->assertEquals($list[0]->getName(), 'Baz');
    }
}