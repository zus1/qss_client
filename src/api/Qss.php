<?php

namespace App\Api;

class Qss extends Call
{
    private function getBaseUrl() {
        return $this->env->get("QSS_BASE", "https://symfony-skeleton.q-tests.com");
    }

    public function login(string $email, string $password) {
        $params = array(
            'email' => $email,
            "password" => $password

        );
        $endpoint = $this->env->get("QSS_LOGIN");
        $url = $this->getBaseUrl() . $endpoint;

        $response = $this->callApi($url, $params, self::METHOD_POST);
        dd($response);
    }
}