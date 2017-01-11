<?php
namespace rtens\steps2\domain;

use rtens\udity\check\DomainSpecification;

class ShowGoalsSpec extends DomainSpecification {

    function noGoals() {
        $this->whenProject(GoalList::class)
            ->assertEquals($this->projection(GoalList::class)->getList(), []);
    }

    function twoGoals() {
        $this->given(Goal::class, 'foo')->created('Foo');
        $this->given(Goal::class, 'bar')->created('Bar');

        $this->whenProject(GoalList::class);

        $goals = $this->projection(GoalList::class)->getList();
        $this->assertEquals(count($goals), 2);
    }

    function hidAchievedGoals() {
        $this->given(Goal::class, 'foo')->created('Foo');
        $this->given(Goal::class, 'bar')->created('Bar');
        $this->given(Goal::class, 'foo')->didAchieve();

        $this->whenProject(GoalList::class);

        $goals = $this->projection(GoalList::class)->getList();
        $this->assertEquals(count($goals), 1);
        $this->assertEquals($goals[0]->getName(), 'Bar');
    }

    function hideGivenUpGoals() {
        $this->given(Goal::class, 'foo')->created('Foo');
        $this->given(Goal::class, 'bar')->created('Bar');
        $this->given(Goal::class, 'foo')->didGiveUp();

        $this->whenProject(GoalList::class);

        $goals = $this->projection(GoalList::class)->getList();
        $this->assertEquals(count($goals), 1);
        $this->assertEquals($goals[0]->getName(), 'Bar');
    }
}