<?php

namespace App\Controller;

use App\Api\Author;
use App\Service\Qss;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthorController extends BaseController
{
    /**
     * @Route("/author-list", name="author_list")
     * @param Qss $qss
     * @param Author $author
     * @return Response
     */
    public function listAuthors(Qss $qss, Author $author): Response {
        $authors = array();
        try {
            $authors = $qss->setCallClass($author)->authors();
            $authors = $qss->addNumberOfBooksForAuthors($authors);
        } catch(Exception $e) {
            $this->addFlash('warning', $e->getMessage());
        }

        return $this->render('author/list.html.twig', [
            'authors' => $authors,
        ]);
    }

    /**
     * @Route("/author-preview/{authorId}", name="author_preview")
     * @param int $authorId
     * @param Qss $qss
     * @param Author $author
     * @param ValidatorInterface $validator
     * @return RedirectResponse|Response
     */
    public function previewAuthor(int $authorId, Qss $qss, Author $author, ValidatorInterface $validator) {
        try {
            $this->makeValidation($validator, (new \App\Entity\Author()), "id", $authorId);
            $authorWithBooks = $qss->setCallClass($author)->authorWithBooks($authorId);
        } catch (Exception $e) {
            $this->addFlash('warning', $e->getMessage());
            return $this->redirectToRoute('author_list');
        }

        $rAuthor = $authorWithBooks["author"];
        $books = $authorWithBooks["books"];

        return $this->render('author/preview.html.twig', [
            'author' => $rAuthor,
            'books' => $books
        ]);
    }

    /**
     * @Route("/author-delete/{authorId}", name="author_delete")
     * @param int $authorId
     * @param Qss $qss
     * @param Author $author
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function ajaxDeleteAuthor(int $authorId, Qss $qss, Author $author, ValidatorInterface $validator) {
        try {
            $this->makeValidation($validator, (new \App\Entity\Author()), "id", $authorId);
            $qss->setCallClass($author)->authorDelete($authorId);
        } catch (Exception $e) {
            return new JsonResponse(['error' => 1, "message" => $e->getMessage()]);
        }

        return new JsonResponse(['error' => 0, "message" => "Author deleted", "author_id" => $authorId]);
    }
}
