<?php

namespace App\Controller;

use App\Api\Qss;
use App\Entity\User;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     */
    public function login(): Response
    {
        return $this->render('auth/login.html.twig');
    }

    /**
     * @Route("/do-login", name="do-login")
     * @param Request $request
     * @param Qss $qss
     * @param ValidatorInterface $validator
     * @param User $user
     * @return Response
     */
    public function doLogin(Request $request, Qss $qss, ValidatorInterface $validator, User $user): Response
    {
        $email = $request->get("email");
        $password = $request->get("password");

        try {
            $this->makeValidation($validator, $user, "email", $email);
            $this->makeValidation($validator, $user, "password", $password);
        } catch(Exception $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('login');
        }

        $res = $qss->login($email, $password);
        dd($res);
    }

    private function makeValidation(ValidatorInterface $validator, User $user, string $name, string $value) {
        $failed = $validator->validatePropertyValue($user, $name, $value);
        if($failed->count()) {
            throw new Exception($failed->get(0)->getMessage());
        }
    }
}
