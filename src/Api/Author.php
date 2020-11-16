<?php

namespace App\Api;

use App\Service\Package;

class Author extends Qss
{
    public function getAuthors() : array {
        $endpoint = $this->env->get("QSS_AUTHORS", "/api/v2/authors");
        $url = $this->getBaseUrl() . $endpoint;
        $response = $this->callQss($url);
        $this->handleError($response);

        return $this->package->package(Package::PACKAGE_AUTHORS, $response);
    }

    public function getAuthorWithBooks(int $authorId) : array {
        $endpoint = $this->env->get("QSS_AUTHOR_BOOKS", "/api/v2/authors/{author}");
        $endpoint = str_replace("{author}", $authorId, $endpoint);
        $url = $this->getBaseUrl() . $endpoint;
        $response = $this->callQss($url);
        $this->handleError($response);

        return $this->package->package(Package::PACKAGE_AUTHOR_BOOKS, $response);
    }

    public function deleteAuthor($authorId) : void {
        $endpoint = $this->env->get("QSS_AUTHOR_DELETE", "/api/v2/authors/{author}");
        $endpoint = str_replace("{author}", $authorId, $endpoint);
        $url = $this->getBaseUrl() . $endpoint;
        $response = $this->callQss($url, array(), self::METHOD_DELETE);
        $this->handleError($response);
    }

    public function addAuthor(\App\Entity\Author $author) : void {
        $author = $author->toArray();
        $endpoint = $this->env->get("QSS_AUTHOR_ADD", "/api/v2/authors");
        $url = $this->getBaseUrl() . $endpoint;
        $response = $this->callQss($url, $author, self::METHOD_POST);
        $this->handleError($response);
    }
}