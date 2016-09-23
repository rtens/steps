<?php namespace rtens\steps\model;

abstract class Identifier {

    private $key;

    public static $makeUnique = true;

    /**
     * @param string $key
     */
    public function __construct($key) {
        $this->key = trim($key);
    }

    /**
     * @param string[] $from
     * @return static
     */
    public static function make($from = []) {
        $transformed = strtolower(implode('_', array_map(function ($piece) {
            return preg_replace('/[^a-zA-Z0-9_]/', '', $piece);
        }, $from)));
        return new static($transformed . (self::$makeUnique ? '_' . uniqid() : ''));
    }

    function __toString() {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getKey() {
        return $this->key;
    }
}