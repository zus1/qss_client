<?php

namespace App\Api;

use App\Services\Cache;
use Exception;

class Qss extends Call
{
    protected function getBaseUrl() {
        return $this->env->get("QSS_BASE", "https://symfony-skeleton.q-tests.com");
    }

    protected function callQss(string $url, ?array $params=array(), ?string $method=self::METHOD_GET) {
        //$apiKey = $this->getApiKey();
        //$this->addHeader("Authorization", "Bearer " . $apiKey);
        $this->addHeader("Authorization", "Bearer ef26fee3f2cda643deed0060a07135882a11aacf");
        return $this->callApi($url, $params, $method);
    }

    protected function getApiKey() {
        $cachedUser = Cache::load()->get(Cache::USER_CACHE_KEY);
        if(!$cachedUser) {
            throw new Exception("Not logged in");
        }
        return $cachedUser->getToken();
    }

    protected function handleError(array $response) {
        if(isset($response["error"]) && $response["error"] === 1) {
            throw new Exception($response["message"], $response["code"]);
        }
    }
}