<?php
namespace rtens\steps2\domain\rank;

use rtens\steps2\domain\Goal;
use rtens\steps2\domain\GoalIdentifier;
use rtens\steps2\domain\Path;
use rtens\udity\utils\Time;

class RankByPenaltySpec extends RankSpecification {

    function noTrack() {
        $this->assertRank(0);
    }

    function noPenalty() {
        $this->given(Path::class)->created(Time::at('today'));
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('foo'), 1, false);
        $this->given(Path::class)->didTakeNextStep();
        $this->given(Path::class)->didCompleteStep();

        Time::freeze('7 days');
        $this->assertRank(0);
    }

    function maximumPenalty() {
        $this->given(Path::class)->created(Time::at('today'));
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('foo'), 1, false);
        $this->given(Path::class)->didTakeNextStep();
        $this->given(Path::class)->didCompleteStep();

        Time::freeze('37 days');
        $this->assertRank(30);
    }

    function keepMaximumPenalty() {
        $this->given(Path::class)->created(Time::at('today'));
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('foo'), 1, false);
        $this->given(Path::class)->didTakeNextStep();
        $this->given(Path::class)->didCompleteStep();

        Time::freeze('100 days');
        $this->assertRank(30);
    }

    function twoTracks() {
        Time::freeze('2001-01-01');
        $this->given(Path::class)->created(Time::at('today'));
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('foo'), 2, true);
        $this->given(Path::class)->didTakeNextStep();
        $this->given(Path::class)->didCompleteStep();

        Time::freeze('2001-01-02');
        $this->given(Path::class)->didTakeNextStep();
        $this->given(Path::class)->didCompleteStep();

        Time::freeze('2001-01-10');
        $this->assertRank(1);
    }

    function otherGoal() {
        Time::freeze('2001-01-01');
        $this->given(Path::class)->created(Time::at('today'));
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('foo'), 1, true);
        $this->given(Path::class)->didTakeNextStep();
        $this->given(Path::class)->didCompleteStep();

        Time::freeze('2001-01-02');
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('bar'), 1, true);
        $this->given(Path::class)->didTakeNextStep();
        $this->given(Path::class)->didCompleteStep();

        Time::freeze('2001-01-10');
        $this->assertRank(2);
    }

    function twoPaths() {
        Time::freeze('2001-01-02');
        $this->given(Path::class)->created(Time::at('today'));
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('foo'), 1, true);
        $this->given(Path::class)->didTakeNextStep();
        $this->given(Path::class)->didCompleteStep();

        Time::freeze('2001-01-01');
        $this->given(Path::class, 'two')->created(Time::at('today'));
        $this->given(Path::class, 'two')->didPlanStep(new GoalIdentifier('foo'), 1, true);
        $this->given(Path::class, 'two')->didTakeNextStep();
        $this->given(Path::class, 'two')->didCompleteStep();

        Time::freeze('2001-01-10');
        $this->assertRank(1);
    }

    function inheritPenalty() {
        $this->given(Goal::class, 'bar')->created('Bar');
        $this->given(Goal::class, 'foo')->didMove(new GoalIdentifier('bar'));

        $this->given(Path::class)->created(Time::at('today'));
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('bar'), 1, false);
        $this->given(Path::class)->didTakeNextStep();
        $this->given(Path::class)->didCompleteStep();

        Time::freeze('22 days');
        $this->assertRank(15);
    }

    function getPenaltyFromChild() {
        $this->given(Goal::class, 'bar')->created('Bar');
        $this->given(Goal::class, 'bar')->didMove(new GoalIdentifier('foo'));

        $this->given(Path::class)->created(Time::at('today'));
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('bar'), 1, false);
        $this->given(Path::class)->didTakeNextStep();
        $this->given(Path::class)->didCompleteStep();

        Time::freeze('22 days');
        $this->assertRank(15);
    }

    function getPenaltyFromChildren() {
        Time::freeze('2001-01-01');
        $this->given(Goal::class, 'bar')->created('Bar');
        $this->given(Goal::class, 'bar')->didMove(new GoalIdentifier('foo'));
        $this->given(Path::class)->created(Time::at('today'));
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('bar'), 1, false);
        $this->given(Path::class)->didTakeNextStep();
        $this->given(Path::class)->didCompleteStep();

        Time::freeze('2001-01-02');
        $this->given(Goal::class, 'baz')->created('Baz');
        $this->given(Goal::class, 'baz')->didMove(new GoalIdentifier('foo'));
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('baz'), 1, false);
        $this->given(Path::class)->didTakeNextStep();
        $this->given(Path::class)->didCompleteStep();

        Time::freeze('2001-01-10');
        $this->assertRank(1);
    }

    function inheritPenaltyCalculatedFromSiblings() {
        Time::freeze('2001-01-01');
        $this->given(Goal::class, 'bar')->created('Bar');
        $this->given(Goal::class, 'foo')->didMove(new GoalIdentifier('bar'));
        $this->given(Path::class)->created(Time::at('today'));

        Time::freeze('2001-01-02');
        $this->given(Goal::class, 'baz')->created('Baz');
        $this->given(Goal::class, 'baz')->didMove(new GoalIdentifier('bar'));
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('baz'), 1, false);
        $this->given(Path::class)->didTakeNextStep();
        $this->given(Path::class)->didCompleteStep();

        Time::freeze('2001-01-10');
        $this->assertRank(1);
    }
}