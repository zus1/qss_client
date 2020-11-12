<?php
declare(strict_types = 1);

namespace App\Service;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Authentication
{
    private $session;

    public function __construct(SessionInterface $session) {
        $this->session = $session;
    }

    public function logout() {
        $userEmail = $this->session->get("user_email");
        if($userEmail) {
            Cache::load()->delete(Cache::USER_CACHE_KEY, array("email" => $userEmail));
            $this->session->remove("user_email");
        }
    }

    public function isAuthenticated() {
        $userEmail = $this->session->get("user_email");
        if(!$userEmail) {
            return false;
        }
        $user = Cache::load()->get(Cache::USER_CACHE_KEY, array("email" => $userEmail));
        if(!$user) {
            if($userEmail) {
                $this->session->remove("user_email");
            }
            return false;
        }

        return true;
    }

    public function getAuthenticatedUser() {
        if(!$this->isAuthenticated()) {
            return new User();
        }
        $userEmail = $this->session->get("user_email");
        $user = Cache::load()->get(Cache::USER_CACHE_KEY, array("email" => $userEmail));

        return $user;
    }

    public function getAuthenticatedUserName() {
        $authUser = $this->getAuthenticatedUser();
        if(!empty($authUser->getName())) {
            return $authUser->getName();
        }

        return "";
    }

    public function getAuthenticatedUserLastName() {
        $authUser = $this->getAuthenticatedUser();
        if(!empty($authUser->getLName())) {
            return $authUser->getLName();
        }

        return "";
    }
}