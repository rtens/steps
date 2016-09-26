<?php namespace rtens\steps;

use rtens\steps\projecting\GoalList;
use watoki\karma\implementations\commandQuery\Query;

class ListGoals implements Query {
    /**
     * @var boolean
     */
    private $showPlanned;
    /**
     * @var null|bool
     */
    private $filterAchieved;

    /**
     * @param bool $showPlanned
     * @param null|bool $filterAchieved
     */
    public function __construct($showPlanned = false, $filterAchieved = true) {
        $this->showPlanned = $showPlanned;
        $this->filterAchieved = $filterAchieved;
    }

    /**
     * @return object
     */
    public function getProjection() {
        $goalList = new GoalList($this->showPlanned);
        if (!is_null($this->filterAchieved)) {
            $goalList->filterAchieved(!$this->filterAchieved);
        }
        return $goalList;
    }
}