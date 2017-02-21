<?php
namespace rtens\steps2\domain;

use rtens\udity\domain\query\DefaultProjection;

class Tracks extends DefaultProjection {

    public function getTracks() {
        return [];
    }

    public function getTotalHours() {
        return 0;
    }
}