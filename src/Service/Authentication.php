<?php
declare(strict_types = 1);

namespace App\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Authentication
{
    const USER_SESSION_KEY = "user_email";
    private $session;

    public function __construct(SessionInterface $session) {
        $this->session = $session;
    }

    public function logout() {
        $userEmail = $this->session->get(self::USER_SESSION_KEY);
        if($userEmail) {
            Cache::load()->delete(Cache::USER_CACHE_KEY, array("email" => $userEmail));
            $this->session->remove("user_email");
        }
    }

    public function isAuthenticated() {
        $userEmail = $this->session->get(self::USER_SESSION_KEY);
        if(!$userEmail) {
            return false;
        }
        $user = Cache::load()->get(Cache::USER_CACHE_KEY, array("email" => $userEmail));
        if(!$user) {
            if($userEmail) {
                $this->session->remove(self::USER_SESSION_KEY);
            }
            return false;
        }

        return true;
    }

    public function getAuthenticatedUser() {
        if(!$this->isAuthenticated()) {
            return null;
        }
        $userEmail = $this->session->get(self::USER_SESSION_KEY);
        $user = Cache::load()->get(Cache::USER_CACHE_KEY, array("email" => $userEmail));

        return $user;
    }

    public function getAuthenticatedUserName() {
        $authUser = $this->getAuthenticatedUser();
        if(!empty($authUser)) {
            return $authUser->getName();
        }

        return "";
    }

    public function getAuthenticatedUserLastName() {
        $authUser = $this->getAuthenticatedUser();
        if(!empty($authUser)) {
            return $authUser->getLName();
        }

        return "";
    }
}