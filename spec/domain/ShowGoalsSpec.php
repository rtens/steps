<?php
namespace rtens\steps2\domain;

use rtens\udity\check\DomainSpecification;
use rtens\udity\utils\Time;

class ShowGoalsSpec extends DomainSpecification {

    function noGoals() {
        $this->whenProject(GoalList::class);
        $this->assertEquals($this->projection(GoalList::class)->getList(), []);
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

    function hidePlannedGoals() {
        $this->given(Goal::class, 'foo')->created('Foo');
        $this->given(Goal::class, 'bar')->created('Bar');
        $this->given(Path::class)->created(Time::at('today'));
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('foo'), 1, false);

        $this->whenProject(GoalList::class);

        $goals = $this->projection(GoalList::class)->getList();
        $this->assertEquals(count($goals), 1);
        $this->assertEquals($goals[0]->getIdentifier(), new GoalIdentifier('bar'));
    }

    function showPlannedGoalWithoutUpcomingSteps() {
        $this->given(Goal::class, 'foo')->created('Foo');
        $this->given(Path::class)->created(Time::at('yesterday'));
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('foo'), 1, false);
        $this->given(Path::class)->didTakeNextStep();

        $this->whenProject(GoalList::class);

        $goals = $this->projection(GoalList::class)->getList();
        $this->assertEquals(count($goals), 1);
    }

    function showGoalsOfPastPaths() {
        $this->given(Goal::class, 'foo')->created('Foo');
        $this->given(Path::class)->created(Time::at('yesterday'));
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('foo'), 1, false);

        $this->whenProject(GoalList::class);

        $goals = $this->projection(GoalList::class)->getList();
        $this->assertEquals(count($goals), 1);
    }

    function hideGoalsOfFuturePaths() {
        $this->given(Goal::class, 'foo')->created('Foo');
        $this->given(Path::class)->created(Time::at('tomorrow'));
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('foo'), 1, false);

        $this->whenProject(GoalList::class);

        $goals = $this->projection(GoalList::class)->getList();
        $this->assertEquals(count($goals), 0);
    }

    function sortOptionsOpenGoalsFirst() {
        $this->given(Goal::class, 'foo')->created('Foo');
        $this->given(Goal::class, 'bar')->created('Bar');
        $this->given(Goal::class, 'baz')->created('Baz');
        $this->given(Goal::class, 'foo')->didAchieve();
        $this->given(Goal::class, 'baz')->didAchieve();

        $this->whenProject(GoalList::class);
        $this->assertEquals(array_keys($this->projection(GoalList::class)->options())[0], 'bar');
        $this->assertEquals(array_keys($this->projection(GoalList::class)->options())[1], 'baz');
        $this->assertEquals(array_keys($this->projection(GoalList::class)->options())[2], 'foo');
    }
}