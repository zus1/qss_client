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
        $mc->addServer(Env::load()->get("MEMCACHED_SERVER", "memcached"), 11211);
        $this->mc = $mc;
    }

    /**
     * @return Cache
     */
    public static function load() : Cache {
        if(empty(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * @param string $key
     * @param $value
     * @param array|null $hash
     * @param int|null $ttl
     */
    public function set(string $key, $value, ?array $hash=array(), ?int $ttl=60) : void {
        $key = $this->getKeyWithHash($key, $hash);
        $this->mc->set($key, $value, $ttl);
    }

    /**
     * @param string $key
     * @param array|null $hash
     * @return mixed
     */
    public function get(string $key, ?array $hash=array()) {
        $key = $this->getKeyWithHash($key, $hash);
        return $this->mc->get($key);
    }

    /**
     * @param string $key
     * @param array|null $hash
     */
    public function delete(string $key, ?array $hash=array()) : void {
        $key = $this->getKeyWithHash($key, $hash);
        $this->mc->delete($key);
    }

    /**
     *
     * Adds hash to key, if any
     *
     * @param string $key
     * @param array $hash
     * @return string
     */
    private function getKeyWithHash(string $key, array $hash) : string {
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