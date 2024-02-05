<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserAdvertisementsController extends AbstractController
{
    #[Route('/user/{id}/advertisements', name: 'app_user_advertisements')]
    public function index(UserRepository $userRepository, int $id, Request $request, PaginatorInterface $paginator): Response
    {
        $user = $userRepository->findOneBy(['id' => $id]);
        $pagination = $paginator->paginate(
            $user->getAdvertisements(),
            $request->query->getInt('page', 1),
            15
        );

        return $this->render('user_advertisements/index.html.twig', [
            'advertisements' => $user->getAdvertisements(),
            'user' => $user,
            'pagination' => $pagination,
        ]);
    }
}
