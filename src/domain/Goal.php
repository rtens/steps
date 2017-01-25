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
     * @var null|Rating
     */
    private $rating;
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
     * @param string $name
     * @param GoalIdentifier|null $parent
     * @param GoalIdentifier[]|null $links
     * @param Rating|null $rating
     * @param \DateTimeImmutable|null $deadline
     * @param Quota|null $quota
     * @param Html|null $note
     */
    public function create($name,
                            GoalIdentifier $parent = null,
                            array $links = null,
                            Rating $rating = null,
                            \DateTimeImmutable $deadline = null,
                            Quota $quota = null,
                            Html $note = null) {

        $this->recordThat('Created', ['name' => $name]);

        if (!is_null($parent)) {
            $this->recordThat('DidMove', ['parent' => $parent]);
        }
        if (!is_null($links)) {
            foreach ($links as $link) {
                $this->recordThat('DidLink', ['to' => $link]);
            }
        }
        if (!is_null($rating)) {
            $this->recordThat('DidRate', ['rating' => $rating]);
        }
        if (!is_null($deadline)) {
            $this->recordThat('ChangedDeadline', ['deadline' => $deadline]);
        }
        if (!is_null($quota)) {
            $this->recordThat('ChangedQuota', ['quota' => $quota]);
        }
        if (!is_null($note)) {
            $this->recordThat('DidAddNote', ['note' => $note]);
        }
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

    public function setName($name) {
        $this->name = $name;
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

    public function didRate(Rating $rating) {
        $this->rating = $rating;
    }

    public function getRating() {
        return $this->rating;
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
        return ($this->deadline->getTimestamp() - Time::now()->getTimestamp()) / 86400;
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

    private function guardStillOpen() {
        if ($this->achieved) {
            throw new \Exception('Goal is already achieved');
        }
        if ($this->givenUp) {
            throw new \Exception('Goal was already given up');
        }
    }
}