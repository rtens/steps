<?php
namespace rtens\steps2\domain;

use rtens\steps2\FakeEvent;
use rtens\udity\check\DomainSpecification;
use rtens\udity\utils\Time;

class ShowTracksSpec extends DomainSpecification {

    function noTracks() {
        $this->whenProject(Tracks::class);

        $tracks = $this->projection(Tracks::class);
        $this->assert()->equals($tracks->getTracks(), []);
        $this->assert()->equals($tracks->getTotalHours(), 0);
    }

    function someTracks() {
        $this->given(Path::class)->created(Time::at('today'));
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('one'), 2, true);
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('two'), 1, true);
        Time::freeze('12:00');
        $this->given(Path::class)->didTakeNextStep(new FakeEvent());
        Time::freeze('12:30');
        $this->given(Path::class)->didCompleteStep(new FakeEvent());
        Time::freeze('14:00');
        $this->given(Path::class)->didTakeNextStep(new FakeEvent());
        Time::freeze('15:30');
        $this->given(Path::class)->didCompleteStep(new FakeEvent());
        Time::freeze('16:00');
        $this->given(Path::class)->didTakeNextStep(new FakeEvent());
        Time::freeze('16:15 ');
        $this->given(Path::class)->didCompleteStep(new FakeEvent());

        $this->given(Path::class, 'bar')->created(Time::at('today'));
        $this->given(Path::class, 'bar')->didPlanStep(new GoalIdentifier('two'), 1, true);
        Time::freeze('13:00');
        $this->given(Path::class)->didTakeNextStep(new FakeEvent());
        Time::freeze('14:30');

        $this->whenProject(Tracks::class);

        $tracks = $this->projection(Tracks::class);
        $this->assert()->equals($tracks->getTracks(), [
            [
                'goal' => new GoalIdentifier('one'),
                'started' => Time::at('12:00'),
                'completed' => Time::at('12:30'),
                'hours' => 0.5
            ],
            [
                'goal' => new GoalIdentifier('two'),
                'started' => Time::at('13:00'),
                'completed' => Time::at('14:30'),
                'hours' => 1.5
            ],
            [
                'goal' => new GoalIdentifier('one'),
                'started' => Time::at('14:00'),
                'completed' => Time::at('15:30'),
                'hours' => 1.5
            ],
            [
                'goal' => new GoalIdentifier('two'),
                'started' => Time::at('16:00'),
                'completed' => Time::at('15:15'),
                'hours' => 0.25
            ]
        ]);
        $this->assert()->equals($tracks->getTotalHours(), 3.75);
    }

    function limitTimeSpan() {
    }

    function defaultTimeSpan() {
    }

    function limitParent() {
    }

    function slidingWindowSum() {
    }
}