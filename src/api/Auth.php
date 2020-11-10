<?php

namespace App\Api;

use App\Services\Package;

class Auth extends Qss
{
    public function login(string $email, string $password) {
        $params = array(
            'email' => $email,
            "password" => $password

        );
        $endpoint = $this->env->get("QSS_LOGIN");
        $url = $this->getBaseUrl() . $endpoint;

        $response = $this->callApi($url, $params, self::METHOD_POST);
        $this->handleError($response);

        return $this->package->package(Package::PACKAGE_USER, $response);
    }
}