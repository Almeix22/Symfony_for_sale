<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $categoryRepository->findBy([], ['name' => 'ASC']),
        ]);
    }

    #[Route('/category/{id}', name: 'app_category_show', requirements: ['id' => '\d+'])]
    public function show(Request $request, PaginatorInterface $paginator, CategoryRepository $categoryRepository, Category $category): Response
    {
        $query = $category->getAdvertisements();
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('category/show.html.twig', [
            'pagination' => $pagination,
            'categorie' => $category->getName(),
        ]);
    }
}
