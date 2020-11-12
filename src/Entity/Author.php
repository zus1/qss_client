<?php
declare(strict_types = 1);

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Exception;

class Author
{
    private int $id;
    private string $name;
    private string $lName;
    private string $birthDay;
    private string $biography;
    private string $gender;
    private string $placeOfBirth;
    private int $numberOfBooks;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

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
    public function getBirthDay(): String
    {
        return $this->birthDay;
    }

    /**
     * @param String $birthDay
     * @throws Exception
     */
    public function setBirthDay(String $birthDay): void
    {
        $dt = new DateTime($birthDay);
        $this->birthDay = $dt->format("Y-m-d");
    }

    /**
     * @return String
     */
    public function getBiography(): String
    {
        return $this->biography;
    }

    /**
     * @param String $biography
     */
    public function setBiography(String $biography): void
    {
        $this->biography = $biography;
    }

    /**
     * @return String
     */
    public function getGender(): String
    {
        return $this->gender;
    }

    /**
     * @param String $gender
     */
    public function setGender(String $gender): void
    {
        $this->gender = $gender;
    }

    /**
     * @return String
     */
    public function getPlaceOfBirth(): String
    {
        return $this->placeOfBirth;
    }

    /**
     * @param String $placeOfBirth
     */
    public function setPlaceOfBirth(String $placeOfBirth): void
    {
        $this->placeOfBirth = $placeOfBirth;
    }

    /**
     * @return int
     */
    public function getNumberOfBooks(): int
    {
        if(empty($this->numberOfBooks)) {
            return 0;
        }
        return $this->numberOfBooks;
    }

    /**
     * @param int $numberOfBooks
     */
    public function setNumberOfBooks(int $numberOfBooks): void
    {
        $this->numberOfBooks = $numberOfBooks;
    }

    public function toArray() {
        $array = array(
            'first_name' => $this->getName(),
            'last_name' => $this->getLName(),
            'gender' => $this->getGender(),
            "place_of_birth" => $this->getPlaceOfBirth(),
            "biography" => $this->getBiography(),
        );
        $dt = new DateTime($this->birthDay);
        $array["birthday"] = $dt->format(DateTimeInterface::RFC3339_EXTENDED);

        return $array;
    }
}