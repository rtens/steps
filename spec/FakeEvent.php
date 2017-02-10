<?php
namespace rtens\steps2;

use rtens\udity\AggregateIdentifier;
use rtens\udity\Event;

class FakeEvent extends Event {

    public function __construct() {
        parent::__construct(new _FakeAggregateIdentifier('foo'), 'Foo');
    }
}

class _FakeAggregateIdentifier extends AggregateIdentifier {
}