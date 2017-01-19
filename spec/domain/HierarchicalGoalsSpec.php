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

    function listAncestorsInOptions() {
        $this->given(Goal::class, 'foo')->created('Foo');
        $this->given(Goal::class, 'bar')->created('Bar');
        $this->given(Goal::class, 'baz')->created('Baz');

        $this->given(Goal::class, 'foo')->doMove(null);
        $this->given(Goal::class, 'bar')->doMove(new GoalIdentifier('foo'));
        $this->given(Goal::class, 'baz')->doMove(new GoalIdentifier('bar'));

        $this->whenProject(GoalList::class);
        $this->assertEquals($this->projection(GoalList::class)->options(), [
            'foo' => 'Foo',
            'bar' => 'Foo: Bar',
            'baz' => 'Foo: Bar: Baz'
        ]);
    }

    function onlyListLeafGoals() {
        $this->given(Goal::class, 'bar')->created('Bar');
        $this->given(Goal::class, 'bar')->doMove(new GoalIdentifier('foo'));

        $this->given(Goal::class, 'baz')->created('Baz');
        $this->given(Goal::class, 'baz')->doMove(new GoalIdentifier('bar'));

        $this->given(Goal::class, 'foo')->created('Foo');

        $this->whenProject(GoalList::class);

        $goals = $this->projection(GoalList::class)->getList();
        $this->assertEquals(count($goals), 1);
        $this->assertEquals($goals[0]->getName(), 'Baz');
    }

    function showGoalsWithOnlyAchievedChildren() {
        $this->given(Goal::class, 'foo')->created('Foo');
        $this->given(Goal::class, 'bar')->created('Bar');
        $this->given(Goal::class, 'baz')->created('Baz');

        $this->given(Goal::class, 'bar')->doMove(new GoalIdentifier('foo'));
        $this->given(Goal::class, 'baz')->doMove(new GoalIdentifier('bar'));
        $this->given(Goal::class, 'baz')->didAchieve();

        $this->whenProject(GoalList::class);

        $goals = $this->projection(GoalList::class)->getList();
        $this->assertEquals(count($goals), 1);
        $this->assertEquals($goals[0]->getName(), 'Bar');
    }

    function hideGoalsWithAchievedParent() {
        $this->given(Goal::class, 'foo')->created('Foo');
        $this->given(Goal::class, 'foo')->didAchieve();

        $this->given(Goal::class, 'bar')->created('Bar');
        $this->given(Goal::class, 'bar')->doMove(new GoalIdentifier('foo'));

        $this->given(Goal::class, 'baz')->created('Baz');
        $this->given(Goal::class, 'baz')->doMove(new GoalIdentifier('bar'));

        $this->whenProject(GoalList::class);

        $goals = $this->projection(GoalList::class)->getList();
        $this->assertEquals(count($goals), 0);
    }
}