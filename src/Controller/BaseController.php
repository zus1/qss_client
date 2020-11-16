<?php
declare(strict_types = 1);

namespace App\Controller;

use App\Service\Authentication;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BaseController extends AbstractController {

    /**
     * @Route("/", name="root")
     * @param Authentication $auth
     * @return RedirectResponse
     */
    public function root(Authentication $auth) : RedirectResponse {
        if($auth->isAuthenticated()) {
            return $this->redirectToRoute("author_list");
        }

        return $this->redirectToRoute("login");
    }

    /**
     * @param ValidatorInterface $validator
     * @param $entity
     * @param string $name
     * @param $value
     * @throws Exception
     */
    protected function makeValidation(ValidatorInterface $validator, $entity, string $name, $value) : void {
        $failed = $validator->validatePropertyValue($entity, $name, $value);
        if($failed->count() > 0) {
            throw new Exception($failed->get(0)->getMessage());
        }
    }

}