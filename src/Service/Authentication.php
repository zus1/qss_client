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

    /**
     * Deletes User from cache and removes session
     */
    public function logout() : void {
        $userEmail = $this->session->get(self::USER_SESSION_KEY);
        if($userEmail) {
            Cache::load()->delete(Cache::USER_CACHE_KEY, array("email" => $userEmail));
            $this->session->remove("user_email");
        }
    }

    /**
     *
     * Checks if there is a authenticated user
     *
     * @return bool
     */
    public function isAuthenticated() : bool {
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

    /**
     *
     * Returns currently authenticated user
     *
     * @return mixed|null
     */
    public function getAuthenticatedUser() {
        if(!$this->isAuthenticated()) {
            return null;
        }
        $userEmail = $this->session->get(self::USER_SESSION_KEY);
        $user = Cache::load()->get(Cache::USER_CACHE_KEY, array("email" => $userEmail));

        return $user;
    }

    /**
     *
     * If there is authenticated user, returns his first name
     *
     * @return string
     */
    public function getAuthenticatedUserName() : string {
        $authUser = $this->getAuthenticatedUser();
        if(!empty($authUser)) {
            return $authUser->getName();
        }

        return "";
    }

    /**
     *
     * If there is authenticated user, returns his last name
     *
     * @return string
     */
    public function getAuthenticatedUserLastName() : string {
        $authUser = $this->getAuthenticatedUser();
        if(!empty($authUser)) {
            return $authUser->getLName();
        }

        return "";
    }
}