<?php

namespace App\Controller;

use App\Api\Auth;
use App\Entity\User;
use App\Services\Qss;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthController extends BaseController
{
    /**
     * @Route("/login", name="login")
     * @return Response
     */
    public function login(): Response
    {
        return $this->render('auth/login.html.twig');
    }

    /**
     * @Route("/do-login", name="do_login")
     * @param Request $request
     * @param Qss $qss
     * @param Auth $auth
     * @param ValidatorInterface $validator
     * @param User $user
     * @return Response
     */
    public function doLogin(Request $request, Qss $qss, Auth $auth, ValidatorInterface $validator, User $user): Response
    {
        $email = $request->get("email");
        $password = $request->get("password");

        try {
            $this->makeValidation($validator, $user, "email", $email);
            $this->makeValidation($validator, $user, "password", $password);
            $qss->setCallClass($auth)->authenticateUser($email, $password);
        } catch(Exception $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('login');
        }

        return $this->redirectToRoute('login');
    }
}
