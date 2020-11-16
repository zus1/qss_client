<?php
declare(strict_types = 1);

namespace App\Service;

use Symfony\Component\Dotenv\Dotenv;

class Env
{
    private static $_instance = null;

    private function __construct() {
        $dotenv = new Dotenv();
        $dotenv->load(dirname(dirname(__DIR__)) . "/.env");
    }

    /**
     * @return Env
     */
    public static function load(): Env {
        if(empty(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * @param string $key
     * @param string|null $default
     * @return mixed|string
     */
    public function get(string $key, ?string $default="") {
        if(isset($_ENV[$key])) {
            return $_ENV[$key];
        }

        return $default;
    }
}