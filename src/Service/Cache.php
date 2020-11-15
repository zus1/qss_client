<?php
declare(strict_types = 1);

namespace App\Service;

use Memcached;

class Cache
{
    public static $_mock = false;

    const USER_CACHE_KEY = "cache_user_key";
    const AUTHORS_CACHE_KEY = "cache_authors_key";
    const AUTHOR_BOOKS_CACHE_KEY = "cache_author_books_key";

    private static $_instance = null;
    private $mc;

    private function __construct() {
        $mc = new Memcached();
        $mc->addServer("memcached", 11211);
        $this->mc = $mc;
    }

    public static function load() {
        if(empty(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function set(string $key, $value, ?array $hash=array(), ?int $ttl=60) {
        $key = $this->getKeyWithHash($key, $hash);
        $this->mc->set($key, $value, $ttl);
    }

    public function get(string $key, ?array $hash=array()) {
        $key = $this->getKeyWithHash($key, $hash);
        return $this->mc->get($key);
    }

    public function delete(string $key, ?array $hash=array()) {
        $key = $this->getKeyWithHash($key, $hash);
        $this->mc->delete($key);
    }

    private function getKeyWithHash(string $key, array $hash) {
        if(self::$_mock === true) {
            $key = sprintf("%s_mock", $key);
        }
        if(!empty($hash)) {
            $hash = base64_encode(implode(",", $hash));
            $key = sprintf("%s_%s", $key, $hash);
        }

        return $key;
    }
}