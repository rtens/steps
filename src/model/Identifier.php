<?php namespace rtens\steps\model;

abstract class Identifier {

    private $key;

    public static $makeUnique = true;

    /**
     * @param string $key
     */
    public function __construct($key) {
        $this->key = $key;
    }

    /**
     * @param string $from
     * @return static
     */
    public static function make($from = '') {
        return new static(strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $from) . (self::$makeUnique ? uniqid() : '')));
    }

    function __toString() {
        return $this->key;
    }
}