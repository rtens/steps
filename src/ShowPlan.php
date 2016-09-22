<?php namespace rtens\steps;
use rtens\steps\projecting\Plan;
use watoki\karma\implementations\commandQuery\Query;

class ShowPlan implements Query {

    /**
     * @return object
     */
    public function getProjection() {
        return new Plan();
    }
}