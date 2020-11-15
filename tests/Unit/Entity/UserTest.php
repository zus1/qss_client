<?php

namespace App\Tests;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUserEntity() {
        $name = "bubu";
        $lname = "lubu";
        $email = "bubu@lubu.bubulubu";
        $token = "1234";
        $password = "5678";

        $user = new User();
        $user->setName($name);
        $user->setLName($lname);
        $user->setEmail($email);
        $user->setToken($token);
        $user->setPassword($password);

        $this->assertEquals($name, $user->getName());
        $this->assertEquals($lname, $user->getLName());
        $this->assertEquals($email, $user->getEmail());
        $this->assertEquals($token, $user->getToken());
        $this->assertEquals($password, $user->getPassword());
    }
}
