<?php

namespace App\Api;

use App\Service\Package;

class Author extends Qss
{
    public function getAuthors() {
        $endpoint = $this->env->get("QSS_AUTHORS", "/api/v2/authors");
        $url = $this->getBaseUrl() . $endpoint;
        $response = $this->callQss($url);
        $this->handleError($response);

        return $this->package->package(Package::PACKAGE_AUTHORS, $response);
    }

    public function getAuthorWithBooks(int $authorId) {
        $endpoint = $this->env->get("QSS_AUTHOR_BOOKS", "/api/v2/authors/{author}");
        $endpoint = str_replace("{author}", $authorId, $endpoint);
        $url = $this->getBaseUrl() . $endpoint;
        $response = $this->callQss($url);
        $this->handleError($response);

        return $this->package->package(Package::PACKAGE_AUTHOR_BOOKS, $response);
    }

    public function deleteAuthor($authorId) {
        $endpoint = $this->env->get("QSS_AUTHOR_DELETE", "/api/v2/authors/{author}");
        $endpoint = str_replace("{author}", $authorId, $endpoint);
        $url = $this->getBaseUrl() . $endpoint;
        $response = $this->callQss($url, array(), self::METHOD_DELETE);
        $this->handleError($response);

        return null;
    }

    public function addAuthor(\App\Entity\Author $author) {
        $author = $author->toArray();
        $endpoint = $this->env->get("QSS_AUTHOR_ADD", "/api/v2/authors");
        $url = $this->getBaseUrl() . $endpoint;
        $response = $this->callQss($url, $author, self::METHOD_POST);
        $this->handleError($response);

        return null;
    }
}