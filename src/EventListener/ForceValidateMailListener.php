<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ForceValidateMailListener
{
    private $urlGenerator;
    private $tokenStorage;

    public function __construct(UrlGeneratorInterface $urlGenerator, TokenStorageInterface $tokenStorage)
    {
        $this->urlGenerator = $urlGenerator;
        $this->tokenStorage = $tokenStorage;
    }

    public function __invoke(RequestEvent $event)
    {
        if ($this->tokenStorage->getToken() && $this->tokenStorage->getToken()->getUser()) {
            $user = $this->tokenStorage->getToken()->getUser();
            $routeName = $event->getRequest()->attributes->get('_route');

            // Vérifie que la requête n'est pas une sous-requête et ne redirige pas les routes utiles
            if (!$event->isMainRequest() || $this->isExcludedRoute($routeName)) {
                return new RedirectResponse($this->urlGenerator->generate('app_advertisement'));
            }

            if (!$user->isVerified()) {
                // Redirige vers la page de demande de confirmation de l'adresse e-mail
                $confirmationUrl = $this->urlGenerator->generate('app_check_email');
                $response = new RedirectResponse($confirmationUrl);
                $event->setResponse($response);
            }
        }

        return new RedirectResponse($this->urlGenerator->generate('app_advertisement'));
    }

    private function isExcludedRoute($routeName)
    {
        // Liste des routes à exclure de la redirection
        $excludedRoutes = ['app_check_email', 'app_logout', 'app_verify_email'];

        return in_array($routeName, $excludedRoutes);
    }
}
