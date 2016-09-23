<?php namespace rtens\steps\model;

class Time {

    private static $frozen;

    public static function freeze(\DateTime $when) {
        self::$frozen = $when;
    }

    public static function now() {
        return self::$frozen ?: new \DateTime();
    }

    public static function at($timeString) {
        return new \DateTime('@' . strtotime($timeString, self::now()->getTimestamp()));
    }
}