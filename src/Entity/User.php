<?php


namespace App\Entity;

class User
{
    private String $name;
    private String $lName;
    private String $email;
    private String $token;

    /**
     * @return String
     */
    public function getName(): String
    {
        return $this->name;
    }

    /**
     * @param String $name
     */
    public function setName(String $name): void
    {
        $this->name = $name;
    }

    /**
     * @return String
     */
    public function getLName(): String
    {
        return $this->lName;
    }

    /**
     * @param String $lName
     */
    public function setLName(String $lName): void
    {
        $this->lName = $lName;
    }

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
}