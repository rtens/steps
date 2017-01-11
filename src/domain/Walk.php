<?php
namespace rtens\steps2\domain;

use rtens\udity\domain\command\Singleton;
use rtens\udity\Event;
use rtens\udity\Projection;

class Walk extends Singleton implements Projection {
    /**
     * @var null|PathIdentifier
     */
    private $chosenPath;
    /**
     * @var PathList
     */
    private $paths;

    public function __construct() {
        parent::__construct();
        $this->paths = new PathList();
    }

    /**
     * @return null|PathIdentifier
     */
    public function getChosenPath() {
        $path = $this->chosenPath();
        return $path ? $path->getIdentifier() : null;
    }

    /**
     * @return null|Step
     */
    public function getCurrentStep() {
        $path = $this->chosenPath();
        return $path ? $path->getCurrentStep() : null;
    }

    /**
     * @return int
     */
    public function getRemainingUnits() {
        return array_sum(array_map(function (Step $step) {
            return $step->getUnits();
        }, $this->remainingSteps()));
    }

    /**
     * @return null|Step
     */
    public function getNextStep() {
        if ($this->getCurrentStep()) {
            return null;
        }

        $steps = $this->remainingSteps();
        return $steps ? $steps[0] : null;
    }

    public function handleChoosePath(PathIdentifier $path) {
        $this->recordThat('DidChoosePath', ['path' => $path]);
    }

    public function applyDidChoosePath($path) {
        $this->chosenPath = $path;
    }

    public function apply(Event $event) {
        $this->paths->apply($event);
        return parent::apply($event);
    }

    /**
     * @return null|Path
     */
    private function chosenPath() {
        foreach ($this->paths->getList() as $path) {
            if ($path->getIdentifier() == $this->chosenPath) {
                return $path;
            }
        }
        return null;
    }

    /**
     * @return Step[]
     */
    private function remainingSteps() {
        $path = $this->chosenPath();
        return $path ? $path->getRemainingSteps() : [];
    }
}