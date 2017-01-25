<?php
namespace rtens\steps2\domain\rank;

use rtens\steps2\domain\Goal;
use rtens\steps2\domain\GoalIdentifier;
use rtens\steps2\domain\GoalList;
use rtens\steps2\domain\Path;
use rtens\steps2\domain\Quota;
use rtens\steps2\domain\Rating;
use rtens\udity\utils\Time;

class RankByQuotaSpec extends RankSpecification {

    function before() {
        parent::before();
        $this->given(Goal::class, 'foo')->didRate(new Rating(10, 0));
    }

    function noQuota() {
        $this->assertRank(10);
    }

    function lacksAll() {
        $this->given(Goal::class, 'foo')->setQuota(new Quota(1, 1));
        $this->assertRank(20);
    }

    function lacksHalf() {
        $this->given(Goal::class, 'foo')->setQuota(new Quota(1, 1));
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('foo'), 1, false);
        Time::freeze('12:00');
        $this->given(Path::class)->didTakeNextStep();
        Time::freeze('12:30');
        $this->given(Path::class)->didCompleteStep();

        $this->assertRank(15);
    }

    function combineTracks() {
        $this->given(Goal::class, 'foo')->setQuota(new Quota(1, 1));
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('foo'), 1, true);
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('bar'), 1, true);
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('foo'), 1, true);

        Time::freeze('12:00');
        $this->given(Path::class)->didTakeNextStep();
        Time::freeze('12:15');
        $this->given(Path::class)->didCompleteStep();

        Time::freeze('12:30');
        $this->given(Path::class)->didTakeNextStep();
        Time::freeze('13:05');
        $this->given(Path::class)->didCompleteStep();

        Time::freeze('13:00');
        $this->given(Path::class)->didTakeNextStep();
        Time::freeze('13:15');
        $this->given(Path::class)->didCompleteStep();

        $this->assertRank(15);
    }

    function ignoreTrackOutsideTimeFrame() {
        $this->given(Goal::class, 'foo')->setQuota(new Quota(1, 1));
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('foo'), 3, true);

        Time::freeze('11:00');
        $this->given(Path::class)->didTakeNextStep();
        Time::freeze('11:30');
        $this->given(Path::class)->didCompleteStep();

        Time::freeze('11:45');
        $this->given(Path::class)->didTakeNextStep();
        Time::freeze('12:15');
        $this->given(Path::class)->didCompleteStep();

        Time::freeze('12:30');
        $this->given(Path::class)->didTakeNextStep();
        Time::freeze('12:45');
        $this->given(Path::class)->didCompleteStep();

        Time::freeze('tomorrow 12:00');
        $this->assertRank(15);
    }

    function lacksNone() {
        $this->given(Goal::class, 'foo')->setQuota(new Quota(1, 1));
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('foo'), 1, false);
        Time::freeze('12:00');
        $this->given(Path::class)->didTakeNextStep();
        Time::freeze('13:00');
        $this->given(Path::class)->didCompleteStep();

        $this->assertRank(10);
    }

    function overWorked() {
        $this->given(Goal::class, 'foo')->setQuota(new Quota(1, 1));
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('foo'), 1, false);
        Time::freeze('12:00');
        $this->given(Path::class)->didTakeNextStep();
        Time::freeze('13:30');
        $this->given(Path::class)->didCompleteStep();

        $this->assertRank(5);
    }

    function maximumOverwork() {
        $this->given(Goal::class, 'foo')->setQuota(new Quota(1, 1));
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('foo'), 1, false);
        Time::freeze('12:00');
        $this->given(Path::class)->didTakeNextStep();
        Time::freeze('14:01');
        $this->given(Path::class)->didCompleteStep();

        $this->assertRank(0);
    }

    function proportionalQuota() {
        $this->given(Goal::class, 'foo')->setQuota(new Quota(1, 1));
        $this->given(Goal::class, 'bar')->created('Bar');
        $this->given(Goal::class, 'bar')->didRate(new Rating(10, 0));
        $this->given(Goal::class, 'bar')->setQuota(new Quota(20, 5));

        $goals = $this->whenProject(GoalList::class)->getGoals();
        $this->assertEquals($goals['foo']->getRank(), 12);
        $this->assertEquals($goals['bar']->getRank(), 18);
    }

    function closedGoalsDoNotCount() {
        $this->given(Goal::class, 'foo')->setQuota(new Quota(1, 1));
        $this->given(Goal::class, 'bar')->created('Bar');
        $this->given(Goal::class, 'bar')->didAchieve();
        $this->given(Goal::class, 'bar')->setQuota(new Quota(20, 5));

        $this->assertRank(20);
    }

    function combineTracksOfChildren() {
        $this->given(Goal::class, 'foo')->setQuota(new Quota(1, 1));

        $this->given(Goal::class, 'bar')->created('Bar');
        $this->given(Goal::class, 'bar')->didMove(new GoalIdentifier('foo'));
        $this->given(Goal::class, 'baz')->created('Baz');
        $this->given(Goal::class, 'baz')->didMove(new GoalIdentifier('foo'));

        $this->given(Path::class)->didPlanStep(new GoalIdentifier('bar'), 1, false);
        Time::freeze('12:00');
        $this->given(Path::class)->didTakeNextStep();
        Time::freeze('12:15');
        $this->given(Path::class)->didCompleteStep();
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('baz'), 1, false);
        Time::freeze('12:15');
        $this->given(Path::class)->didTakeNextStep();
        Time::freeze('12:30');
        $this->given(Path::class)->didCompleteStep();

        $this->assertRank(15);
    }

    function inheritLack() {
        $this->given(Goal::class, 'bar')->created('Bar');
        $this->given(Goal::class, 'bar')->setQuota(new Quota(1, 1));
        $this->given(Goal::class, 'foo')->didMove(new GoalIdentifier('bar'));

        $this->given(Path::class)->didPlanStep(new GoalIdentifier('bar'), 1, false);
        Time::freeze('12:00');
        $this->given(Path::class)->didTakeNextStep();
        Time::freeze('12:30');
        $this->given(Path::class)->didCompleteStep();

        $this->assertRank(15);
    }

    function inheritLackCombinedFromSiblings() {
        $this->given(Goal::class, 'bar')->created('Bar');
        $this->given(Goal::class, 'bar')->setQuota(new Quota(1, 1));
        $this->given(Goal::class, 'foo')->didMove(new GoalIdentifier('bar'));

        $this->given(Goal::class, 'baz')->created('Baz');
        $this->given(Goal::class, 'baz')->didMove(new GoalIdentifier('bar'));

        $this->given(Path::class)->didPlanStep(new GoalIdentifier('baz'), 1, false);
        Time::freeze('12:00');
        $this->given(Path::class)->didTakeNextStep();
        Time::freeze('12:15');
        $this->given(Path::class)->didCompleteStep();
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('foo'), 1, false);
        Time::freeze('12:15');
        $this->given(Path::class)->didTakeNextStep();
        Time::freeze('12:30');
        $this->given(Path::class)->didCompleteStep();

        $this->assertRank(15);
    }
}