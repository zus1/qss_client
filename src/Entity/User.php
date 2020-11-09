<?php


namespace App\Entity;

class User
{
    private String $email;
    private String $password;
    private String $token;
    private String $expires;

    /**
     * @return String
     */
    public function getEmail(): String
    {
        return $this->email;
    }

    /**
     * @param String $email
     */
    public function setEmail(String $email): void
    {
        $this->email = $email;
    }

    /**
     * @return String
     */
    public function getPassword(): String
    {
        return $this->password;
    }

    /**
     * @param String $password
     */
    public function setPassword(String $password): void
    {
        $this->password = $password;
    }

    /**
     * @return String
     */
    public function getToken(): String
    {
        return $this->token;
    }

    /**
     * @param String $token
     */
    public function setToken(String $token): void
    {
        $this->token = $token;
    }

    /**
     * @return String
     */
    public function getExpires(): String
    {
        return $this->expires;
    }

    /**
     * @param String $expires
     */
    public function setExpires(String $expires): void
    {
        $this->expires = $expires;
    }


}