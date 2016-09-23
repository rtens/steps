<?php namespace rtens\steps;
use rtens\steps\projecting\CurrentBlocks;
use watoki\karma\implementations\commandQuery\Query;

class ShowCurrentBlocks implements Query {

    /**
     * @return object
     */
    public function getProjection() {
        return new CurrentBlocks();
    }
}