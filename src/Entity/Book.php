<?php

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Exception;

class Book
{
    private int $id;
    private string $title;
    private string $releaseDate;
    private string $isbn;
    private string $format;
    private int $numOfPages;
    private string $description;

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
    public function getTitle(): String
    {
        return $this->title;
    }

    /**
     * @param String $title
     */
    public function setTitle(String $title): void
    {
        $this->title = $title;
    }

    /**
     * @return String
     */
    public function getReleaseDate(): String
    {
        return $this->releaseDate;
    }

    /**
     * @param String $releaseDate
     * @throws Exception
     */
    public function setReleaseDate(String $releaseDate): void
    {
        $dt = new DateTime($releaseDate);
        $this->releaseDate = $dt->format("Y-m-d H:i:s");
    }

    /**
     * @return String
     */
    public function getIsbn(): String
    {
        return $this->isbn;
    }

    /**
     * @param String $isbn
     */
    public function setIsbn(String $isbn): void
    {
        $this->isbn = $isbn;
    }

    /**
     * @return String
     */
    public function getFormat(): String
    {
        return $this->format;
    }

    /**
     * @param String $format
     */
    public function setFormat(String $format): void
    {
        $this->format = $format;
    }

    /**
     * @return int
     */
    public function getNumOfPages(): int
    {
        return $this->numOfPages;
    }

    /**
     * @param int $numOfPages
     */
    public function setNumOfPages(int $numOfPages): void
    {
        $this->numOfPages = $numOfPages;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }



    public function toArray() {
        $array = array(
            'title' => $this->getTitle(),
            'isbn' => $this->getIsbn(),
            'format' => $this->getFormat(),
            'number_of_pages' => $this->getNumOfPages(),
            "description" => $this->getDescription(),
        );
        $dt = new DateTime($this->releaseDate);
        $array["release_date"] = $dt->format(DateTimeInterface::RFC3339_EXTENDED);

        return $array;
    }
}