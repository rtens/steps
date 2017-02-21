<?php
namespace rtens\steps2\domain;

use rtens\udity\domain\query\DefaultProjection;
use rtens\udity\Event;

class Tracks extends DefaultProjection {
    /**
     * @var PathList
     */
    private $paths;
    /**
     * @var null|\DateTimeImmutable
     */
    private $start;
    /**
     * @var null|\DateTimeImmutable
     */
    private $end;
    /**
     * @var null|GoalIdentifier
     */
    private $goal;
    /**
     * @var GoalIdentifier[]
     */
    private $parents = [];

    public function __construct(\DateTimeImmutable $start,
                                \DateTimeImmutable $end = null,
                                GoalIdentifier $goal = null) {
        $this->paths = new PathList();
        $this->start = $start;
        $this->goal = $goal;
        $this->end = $end;
    }

    public function apply(Event $event) {
        parent::apply($event);
        $this->paths->apply($event);
    }

    public function applyDidMove(GoalIdentifier $parent = null, Event $event) {
        $this->parents[$event->getAggregateIdentifier()->getKey()] = $parent;
    }

    private function isDescendant(GoalIdentifier $goal) {
        if ($goal == $this->goal) {
            return true;
        }
        if (!array_key_exists($goal->getKey(), $this->parents) || !$this->parents[$goal->getKey()]) {
            return false;
        }
        if ($this->parents[$goal->getKey()] == $this->goal) {
            return true;
        }
        return $this->isDescendant($this->parents[$goal->getKey()]);
    }

    public function getTracks() {
        $tracks = [];

        foreach ($this->paths->getItems() as $path) {
            foreach ($path->getCompletedSteps() as $step) {
                if ($this->start && $step->getStarted() < $this->start) {
                    continue;
                }
                if ($this->end && $step->getCompleted() > $this->end) {
                    continue;
                }
                if ($this->goal && !$this->isDescendant($step->getGoal())) {
                    continue;
                }

                $tracks[] = [
                    'goal' => $step->getGoal(),
                    'started' => $step->getStarted(),
                    'completed' => $step->getCompleted(),
                    'hours' => ($step->getCompleted()->getTimestamp() - $step->getStarted()->getTimestamp()) / 3600
                ];
            }
        }

        usort($tracks, function ($a, $b) {
            return $a['started'] < $b['started'] ? -1 : 1;
        });

        return $tracks;
    }

    public function getTotalHours() {
        return array_sum(array_map(function ($track) {
            return $track['hours'];
        }, $this->getTracks()));
    }
}