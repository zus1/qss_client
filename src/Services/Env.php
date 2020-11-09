<?php

namespace App\Services;

use Symfony\Component\Dotenv\Dotenv;

class Env
{
    private static $_instance = null;

    private function __construct() {
        $dotenv = new Dotenv();
        $dotenv->load(dirname(dirname(__DIR__)) . "/.env");
    }

    public static function load() {
        if(empty(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function get(string $key, ?string $default="") {
        if(isset($_ENV[$key])) {
            return $_ENV[$key];
        }

        return $default;
    }
}