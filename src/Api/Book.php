<?php

namespace App\Api;

class Book extends Qss
{
    public function addBook(int $authorId, \App\Entity\Book $book) : void {
        $book = $book->toArray();
        $book["author"] = array("id" => $authorId);
        $endpoint = $this->env->get("QSS_BOOK_ADD", "/api/v2/books");
        $url = $this->getBaseUrl() . $endpoint;
        $response = $this->callQss($url, $book, self::METHOD_POST);
        $this->handleError($response);
    }

    public function deleteBook(int $bookId) : void {
        $endpoint = $this->env->get("QSS_BOOK_DELETE", "/api/v2/books/{book}");
        $endpoint = str_replace("{book}", $bookId, $endpoint);
        $url = $this->getBaseUrl() . $endpoint;
        $response = $this->callQss($url, array(), self::METHOD_DELETE);
        $this->handleError($response);
    }
}