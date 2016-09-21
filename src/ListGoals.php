<?php namespace rtens\steps;

use rtens\steps\projecting\GoalList;
use watoki\karma\implementations\commandQuery\Query;

class ListGoals implements Query {

    /**
     * @return object
     */
    public function getProjection() {
        return new GoalList();
    }
}