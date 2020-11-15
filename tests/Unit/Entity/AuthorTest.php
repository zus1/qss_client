<?php

namespace App\Tests;

use App\Entity\Author;
use PHPUnit\Framework\TestCase;

class AuthorTest extends TestCase
{
    public function testAuthorEntity() {
        $name = "bubu";
        $lname = "lubu";
        $biography = "bubu lubub bubu lubub";
        $birthday = "11/11/2020";
        $placeOfBirth = "BubuLubuLand";
        $gender = "male";
        $id = 1;
        $booksNum = 10;

        $author = new Author();
        $author->setName($name);
        $author->setLName($lname);
        $author->setBiography($biography);
        $author->setBirthDay($birthday);
        $author->setPlaceOfBirth($placeOfBirth);
        $author->setGender($gender);
        $author->setId($id);
        $author->setNumberOfBooks($booksNum);

        $this->assertEquals($name, $author->getName());
        $this->assertEquals($lname, $author->getLName());
        $this->assertEquals($biography, $author->getBiography());
        $expectedBirthDay = "2020-11-11";
        $this->assertEquals($expectedBirthDay, $author->getBirthDay());
        $this->assertEquals($placeOfBirth, $author->getPlaceOfBirth());
        $this->assertEquals($gender, $author->getGender());
        $this->assertEquals($id, $author->getId());
        $this->assertEquals($booksNum, $author->getNumberOfBooks());

        $expectedAuthorArr = array(
            'first_name' => $name,
            'last_name' => $lname,
            'gender' => $gender,
            "place_of_birth" => $placeOfBirth,
            "biography" => $biography,
            "birthday" => "2020-11-11T00:00:00.000+00:00"
        );
        $authorArr = $author->toArray();
        $this->assertEquals($expectedAuthorArr, $authorArr);
    }
}
