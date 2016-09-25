<?php
namespace spec\rtens\steps;

use rtens\steps\app\Application;
use rtens\steps\events\BlockFinished;
use rtens\steps\events\BlockPlanned;
use rtens\steps\events\DeadlineSet;
use rtens\steps\events\GoalCreated;
use rtens\steps\events\GoalRated;
use rtens\steps\ListGoals;
use rtens\steps\model\BlockIdentifier;
use rtens\steps\model\GoalIdentifier;
use rtens\steps\model\Time;
use rtens\steps\projecting\Goal;
use rtens\steps\projecting\GoalList;
use rtens\steps\ShowGoal;
use watoki\karma\testing\Specification;

class RankGoalsSpec extends Specification {

    public function __construct() {
        parent::__construct(Application::sandbox());
    }

    public function before() {
        $this->given(new GoalCreated(new GoalIdentifier('foo'), 'Foo', Time::now()));
    }

    public function importance() {
        $this->given(new GoalRated(new GoalIdentifier('foo'), 10, 0, Time::now()));
        $this->when(new ShowGoal(new GoalIdentifier('foo')));
        $this->then->returnShouldMatch(function (Goal $goal) {
            return $goal->getRank() == 10;
        });
    }

    public function urgency() {
        $this->given(new GoalRated(new GoalIdentifier('foo'), 0, 10, Time::now()));
        $this->when(new ShowGoal(new GoalIdentifier('foo')));
        $this->then->returnShouldMatch(function (Goal $goal) {
            return $goal->getRank() == 20;
        });
    }

    public function importanceAndUrgency() {
        $this->given(new GoalRated(new GoalIdentifier('foo'), 10, 10, Time::now()));
        $this->when(new ShowGoal(new GoalIdentifier('foo')));
        $this->then->returnShouldMatch(function (Goal $goal) {
            return $goal->getRank() == 30;
        });
    }

    public function deadlineWithoutEffect() {
        $this->given(new DeadlineSet(new GoalIdentifier('foo'), Time::at('14 days'), Time::now()));
        $this->when(new ShowGoal(new GoalIdentifier('foo')));
        $this->then->returnShouldMatch(function (Goal $goal) {
            return $goal->getRank() == 0;
        });
    }

    public function deadlineHalfwayFromZero() {
        $this->given(new DeadlineSet(new GoalIdentifier('foo'), Time::at('7 days'), Time::now()));
        $this->when(new ShowGoal(new GoalIdentifier('foo')));
        $this->then->returnShouldMatch(function (Goal $goal) {
            return $goal->getRank() == 10;
        });
    }

    public function deadlineFull() {
        $this->given(new DeadlineSet(new GoalIdentifier('foo'), Time::at('0 days'), Time::now()));
        $this->when(new ShowGoal(new GoalIdentifier('foo')));
        $this->then->returnShouldMatch(function (Goal $goal) {
            return $goal->getRank() == 20;
        });
    }

    public function deadlineMissed() {
        $this->given(new DeadlineSet(new GoalIdentifier('foo'), Time::at('1 day ago'), Time::now()));
        $this->when(new ShowGoal(new GoalIdentifier('foo')));
        $this->then->returnShouldMatch(function (Goal $goal) {
            return $goal->getRank() == 20;
        });
    }

    public function deadlineWithBaseUrgency() {
        $this->given(new DeadlineSet(new GoalIdentifier('foo'), Time::at('7 days'), Time::now()));
        $this->given(new GoalRated(new GoalIdentifier('foo'), 0, 6, Time::now()));
        $this->when(new ShowGoal(new GoalIdentifier('foo')));
        $this->then->returnShouldMatch(function (Goal $goal) {
            return $goal->getRank() == 16;
        });
    }

    public function penaltyForDisregard() {
        $this->given(new GoalCreated(new GoalIdentifier('bar'), 'Bar', Time::at('7 days ago')));
        $this->when(new ShowGoal(new GoalIdentifier('bar')));
        $this->then->returnShouldMatch(function (Goal $goal) {
            return $goal->getRank() == 7;
        });
    }

    public function resetPenalty() {
        $this->given(new GoalCreated(new GoalIdentifier('bar'), 'Bar', Time::at('7 days ago')));
        $this->given(new BlockPlanned(new BlockIdentifier('meh'), new GoalIdentifier('bar'), 1, Time::now()));
        $this->given(new BlockFinished(new BlockIdentifier('meh'), Time::at('2 days ago')));
        $this->when(new ShowGoal(new GoalIdentifier('bar')));
        $this->then->returnShouldMatch(function (Goal $goal) {
            return $goal->getRank() == 2;
        });
    }

    public function sortByRank() {
        $this->given(new GoalCreated(new GoalIdentifier('bar'), 'Bar', Time::now()));
        $this->given(new GoalCreated(new GoalIdentifier('baz'), 'Bar', Time::now()));

        $this->given(new GoalRated(new GoalIdentifier('bar'), 0, 3, Time::now()));
        $this->given(new GoalRated(new GoalIdentifier('baz'), 0, 2, Time::now()));
        $this->given(new GoalRated(new GoalIdentifier('foo'), 0, 1, Time::now()));

        $this->when(new ListGoals());
        $this->then->returnShouldMatch(function (GoalList $list) {
            $goals = $list->getGoals();
            return
                $goals[0]->getGoal() == new GoalIdentifier('bar')
                && $goals[1]->getGoal() == new GoalIdentifier('baz')
                && $goals[2]->getGoal() == new GoalIdentifier('foo');
        });
    }
}