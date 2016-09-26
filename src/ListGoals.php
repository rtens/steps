<?php namespace rtens\steps;

use rtens\steps\projecting\GoalList;
use watoki\karma\implementations\commandQuery\Query;

class ListGoals implements Query {
    /**
     * @var boolean
     */
    private $showPlanned;

    /**
     * @param bool $showPlanned
     */
    public function __construct($showPlanned = false) {
        $this->showPlanned = $showPlanned;
    }

    /**
     * @return object
     */
    public function getProjection() {
        return new GoalList($this->showPlanned);
    }
}