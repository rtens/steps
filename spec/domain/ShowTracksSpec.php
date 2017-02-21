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
        $this->given(Path::class)->created(Time::now());
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
        $this->given(Path::class, 'bar')->didTakeNextStep(new FakeEvent());
        Time::freeze('14:30');
        $this->given(Path::class, 'bar')->didCompleteStep(new FakeEvent());

        $this->whenProject(Tracks::class);

        $projection = $this->projection(Tracks::class);
        $this->assert()->equals($projection->getTotalHours(), 3.75);

        $tracks = $projection->getTracks();
        $this->assert()->size($tracks, 4);
        $this->assert()->equals($tracks[0], [
            'goal' => new GoalIdentifier('one'),
            'started' => Time::at('12:00'),
            'completed' => Time::at('12:30'),
            'hours' => 0.5
        ]);
        $this->assert()->equals($tracks[1], [
            'goal' => new GoalIdentifier('two'),
            'started' => Time::at('13:00'),
            'completed' => Time::at('14:30'),
            'hours' => 1.5
        ]);
        $this->assert()->equals($tracks[2], [
            'goal' => new GoalIdentifier('one'),
            'started' => Time::at('14:00'),
            'completed' => Time::at('15:30'),
            'hours' => 1.5
        ]);
        $this->assert()->equals($tracks[3], [
            'goal' => new GoalIdentifier('two'),
            'started' => Time::at('16:00'),
            'completed' => Time::at('16:15'),
            'hours' => 0.25
        ]);
    }

    function limitTimeSpan() {
        $this->given(Path::class)->created(Time::now());
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('one'), 3, true);
        Time::freeze('12:00');
        $this->given(Path::class)->didTakeNextStep(new FakeEvent());
        Time::freeze('13:00');
        $this->given(Path::class)->didCompleteStep(new FakeEvent());
        Time::freeze('13:00');
        $this->given(Path::class)->didTakeNextStep(new FakeEvent());
        Time::freeze('14:00');
        $this->given(Path::class)->didCompleteStep(new FakeEvent());
        Time::freeze('14:00');
        $this->given(Path::class)->didTakeNextStep(new FakeEvent());
        Time::freeze('15:00');
        $this->given(Path::class)->didCompleteStep(new FakeEvent());

        $this->whenProject(Tracks::class, [Time::at('13:00'), Time::at('14:00')]);

        $projection = $this->projection(Tracks::class);
        $this->assert()->equals($projection->getTotalHours(), 1);

        $tracks = $projection->getTracks();
        $this->assert()->size($tracks, 1);
        $this->assert()->equals($tracks[0], [
            'goal' => new GoalIdentifier('one'),
            'started' => Time::at('13:00'),
            'completed' => Time::at('14:00'),
            'hours' => 1
        ]);
    }

    function defaultTimeSpan() {
        $this->given(Path::class)->created(Time::now());
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('one'), 2, true);
        Time::freeze('2001-01-01 11:59');
        $this->given(Path::class)->didTakeNextStep(new FakeEvent());
        Time::freeze('2001-01-01 12:00');
        $this->given(Path::class)->didCompleteStep(new FakeEvent());
        Time::freeze('2001-01-01 12:00');
        $this->given(Path::class)->didTakeNextStep(new FakeEvent());
        Time::freeze('2001-01-01 13:00');
        $this->given(Path::class)->didCompleteStep(new FakeEvent());

        Time::freeze('2001-01-31 12:00');
        $this->whenProject(Tracks::class);

        $projection = $this->projection(Tracks::class);
        $this->assert()->equals($projection->getTotalHours(), 1);

        $tracks = $projection->getTracks();
        $this->assert()->size($tracks, 1);
        $this->assert()->equals($tracks[0]['started'], Time::at('2001-01-01 12:00'));
    }

    function limitParent() {
        $this->given(Goal::class, 'one')->created('One', new FakeEvent());
        $this->given(Goal::class, 'two')->created('Two', new FakeEvent());
        $this->given(Goal::class, 'three')->created('Three', new FakeEvent());
        $this->given(Goal::class, 'four')->created('Four', new FakeEvent());

        $this->given(Goal::class, 'two')->didMove(new GoalIdentifier('one'));
        $this->given(Goal::class, 'four')->didMove(new GoalIdentifier('two'));

        $this->given(Path::class)->created(Time::now());
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('one'), 1, true);
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('two'), 1, true);
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('three'), 1, true);
        $this->given(Path::class)->didPlanStep(new GoalIdentifier('four'), 1, true);
        $this->given(Path::class)->didTakeNextStep(new FakeEvent());
        $this->given(Path::class)->didCompleteStep(new FakeEvent());
        $this->given(Path::class)->didTakeNextStep(new FakeEvent());
        $this->given(Path::class)->didCompleteStep(new FakeEvent());
        $this->given(Path::class)->didTakeNextStep(new FakeEvent());
        $this->given(Path::class)->didCompleteStep(new FakeEvent());
        $this->given(Path::class)->didTakeNextStep(new FakeEvent());
        $this->given(Path::class)->didCompleteStep(new FakeEvent());

        $this->whenProject(Tracks::class, ['goal' => new GoalIdentifier('one')]);

        $projection = $this->projection(Tracks::class);
        $tracks = $projection->getTracks();
        $this->assert()->size($tracks, 3);
        $goals = array_map(function ($track) {
            return $track['goal'];
        }, $tracks);
        $this->assert()->contains($goals, new GoalIdentifier('one'));
        $this->assert()->contains($goals, new GoalIdentifier('two'));
        $this->assert()->contains($goals, new GoalIdentifier('four'));
    }
}