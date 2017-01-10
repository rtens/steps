<?php
namespace rtens\steps2\domain;

use rtens\udity\domain\objects\DomainObject;

class Goal extends DomainObject {

    private $name;

    public function created($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function caption() {
        return $this->getName();
    }
}