<?php

namespace App\EventListener;


use App\Service\Authentication;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RequestListener
{
    private $auth;
    private $urlGenerator;

    private $notAuthBlackListed = array(
        'logout', 'author_list', 'author_preview', 'author_delete', 'book_add', 'book_do_add', 'book_delete'
    );

    private $authBlackListed = array(
        'login', 'do_login'
    );

    public function __construct(Authentication $auth, UrlGeneratorInterface $generator) {
        $this->auth = $auth;
        $this->urlGenerator = $generator;
    }

    public function onKernelRequest(RequestEvent $event) {
        $requestRoute = strtolower($event->getRequest()->get("_route"));
        if(!$this->auth->isAuthenticated()) {
            $nonAuthBlacklisted = $this->notAuthBlackListed;
            $this->applyResponseIfNeeded($event, $nonAuthBlacklisted, $requestRoute, "login");
        }

        if($this->auth->isAuthenticated()) {
            $authBlackListed = $this->authBlackListed;
            $this->applyResponseIfNeeded($event, $authBlackListed, $requestRoute, "author_list");
        }
    }

    private function applyResponseIfNeeded(RequestEvent $event, array $blackListed, string $requestRoute, string $redirectRoute) {
        array_walk($blackListed, function (string $route) use($requestRoute, $event, $redirectRoute) {
            if($route === $requestRoute) {
                $url = $this->urlGenerator->generate($redirectRoute);
                $response = new RedirectResponse($url);
                $event->setResponse($response);
            }
        });
    }
}