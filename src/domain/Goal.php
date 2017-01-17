<?php
namespace rtens\steps2\domain;

use rtens\domin\parameters\Html;
use rtens\udity\domain\objects\DomainObject;
use rtens\udity\utils\Time;

class Goal extends DomainObject {
    /**
     * @var string
     */
    private $name;
    /**
     *
     * @var bool
     */
    private $achieved = false;
    /**
     * @var bool
     */
    private $givenUp = false;
    /**
     * @var null|GoalIdentifier
     */
    private $parent;
    /**
     * @var GoalIdentifier[]
     */
    private $links = [];
    /**
     * @var Html[]
     */
    private $notes = [];
    /**
     * @var int
     */
    private $importance = 0;
    /**
     * @var int
     */
    private $urgency = 0;
    /**
     * @var null|\DateTimeImmutable
     */
    private $deadline;
    /**
     * @var null|Quota
     */
    private $quota;

    /**
     * @return GoalIdentifier|\rtens\udity\AggregateIdentifier
     */
    public function getIdentifier() {
        return parent::getIdentifier();
    }

    /**
     * @return null|GoalIdentifier
     */
    public function getParent() {
        return $this->parent;
    }

    /**
     * @return GoalIdentifier[]
     */
    public function getLinks() {
        return $this->links;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isOpen() {
        return !$this->isAchieved() && !$this->isGivenUp();
    }

    /**
     * @return bool
     */
    public function isAchieved() {
        return $this->achieved;
    }

    /**
     * @return bool
     */
    public function isGivenUp() {
        return $this->givenUp;
    }

    /**
     * @param [0;10] $importance
     * @param [0;10] $urgency
     */
    public function didRate($importance, $urgency) {
        $this->importance = $importance;
        $this->urgency = $urgency;
    }

    /**
     * @return int
     */
    public function getImportance() {
        return $this->importance;
    }

    /**
     * @return int
     */
    public function getUrgency() {
        return $this->urgency;
    }

    /**
     * @return null|Quota
     */
    public function getQuota() {
        return $this->quota;
    }

    /**
     * @param null|Quota $quota
     */
    public function setQuota($quota) {
        $this->quota = $quota;
    }

    /**
     * @param \DateTimeImmutable|null $deadline
     */
    public function setDeadline($deadline) {
        $this->deadline = $deadline;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getDeadline() {
        return $this->deadline;
    }

    public function getDaysLeft() {
        if (!$this->deadline) {
            return null;
        }
        return ($this->deadline->getTimestamp() - Time::now()->getTimestamp()) / (24 * 60 * 60);
    }

    public function didAddNote(Html $note) {
        $this->notes[] = $note;
    }

    /**
     * @param Html[] $notes
     */
    public function setNotes($notes) {
        $this->notes = $notes;
    }

    /**
     * @return Html[]
     */
    public function getNotes() {
        return $this->notes;
    }

    public function caption() {
        $caption = $this->getName();
        if ($this->isAchieved()) {
            $caption .= ' (achieved)';
        } else if ($this->isGivenUp()) {
            $caption .= ' (given up)';
        }
        return $caption;
    }

    public function created($name) {
        $this->name = $name;
    }

    public function doAchieve() {
        $this->guardStillOpen();
    }

    public function didAchieve() {
        $this->achieved = true;
    }

    public function doGiveUp() {
        $this->guardStillOpen();
    }

    public function didGiveUp() {
        $this->givenUp = true;
    }

    public function doMove(GoalIdentifier $parent = null) {
        if ($parent == $this->getIdentifier()) {
            throw new \Exception('Goal cannot be its own parent');
        }
    }

    public function didMove(GoalIdentifier $parent = null) {
        $this->parent = $parent;
    }

    public function doLink(GoalIdentifier $to) {
        if ($to == $this->getIdentifier()) {
            throw new \Exception('Cannot link a Goal to itself');
        }
    }

    public function didLink(GoalIdentifier $to) {
        $this->links[] = $to;
    }

    private function guardStillOpen() {
        if ($this->achieved) {
            throw new \Exception('Goal is already achieved');
        }
        if ($this->givenUp) {
            throw new \Exception('Goal was already given up');
        }
    }
}