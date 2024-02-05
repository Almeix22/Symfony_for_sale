<?php

namespace App\Controller;

use App\Entity\Advertisement;
use App\Form\AdvertisementType;
use App\Repository\AdvertisementRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AdvertisementController extends AbstractController
{
    #[Route('/advertisement', name: 'app_advertisement')]
    public function index(Request $request, AdvertisementRepository $advertisementRepository, PaginatorInterface $paginator): Response
    {
        $search = $request->query->get('search');
        if (null == $search) {
            $search = '';
        }
        $listAnnonces = $advertisementRepository->search($search);

        $pagination = $paginator->paginate(
            $listAnnonces,
            $request->query->getInt('page', 1),
            15
        );

        return $this->render('advertisement/index.html.twig', [
            'user' => null,
            'pagination' => $pagination,
        ]);
    }

    #[Route('/advertisement/{id}', name: 'app_advertisement_show', requirements: ['id' => '\d+'])]
    public function show(#[MapEntity(expr: 'repository.findOneAdvertisementWithCategoryById(id)')] Advertisement $advertisement): Response
    {
        return $this->render('advertisement/show.html.twig', [
            'advertisement' => $advertisement,
        ]);
    }

    #[Route('/advertisement/new', name: 'app_advertisement_new')]
    #[IsGranted('ROLE_USER')]
    public function new(ManagerRegistry $doctrineContact, Request $request): RedirectResponse|Response
    {
        $advertisement = new Advertisement();
        $form = $this->createForm(AdvertisementType::class, $advertisement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrineContact->getManager();
            $advertisement->setOwner($this->getUser());
            $entityManager->persist($advertisement);
            $entityManager->flush();

            return $this->redirectToRoute('app_advertisement');
        }

        return $this->render('advertisement/_form.html.twig', [
            'advertisement' => $advertisement,
            'form' => $form->createView(),
            'edit' => false,
        ]);
    }

    #[Route('/advertisement/{id}/edit', name: 'app_advertisement_edit')]
    #[IsGranted('ROLE_USER')]
    public function update(ManagerRegistry $doctrine, #[MapEntity(expr: 'repository.findOneAdvertisementWithCategoryById(id)')] Advertisement $advertisement, Request $request, AdvertisementRepository $advertisementRepository): RedirectResponse|Response
    {
        if ($this->isAdvertisementOwnerCurrentUser($advertisement, $advertisementRepository)) {
            $form = $this->createForm(AdvertisementType::class, $advertisement);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $doctrine->getManager();
                $entityManager->persist($advertisement);
                /** @var Advertisement $editAdvertisement */
                $editAdvertisement = $form->getData();
                $entityManager->flush();

                return $this->redirectToRoute('app_advertisement', [
                    'id' => $editAdvertisement->getId(),
                ]);
            }

            return $this->render('advertisement/_form.html.twig', [
                'advertisement' => $advertisement,
                'form' => $form->createView(),
                'edit' => true,
            ]);
        } else {
            return $this->render('advertisement/tu_fais_quoi_la.html.twig', [
                'edit' => true,
            ]);
        }
    }

    #[Route('/advertisement/{id}/delete', name: 'app_advertisement_delete')]
    #[IsGranted('ROLE_USER')]
    public function delete(ManagerRegistry $doctrine, Request $request, int $id, AdvertisementRepository $repository): RedirectResponse|Response
    {
        if ($this->isAdvertisementOwnerCurrentUser($repository->findOneBy(['id' => $id]), $repository)) {
            $advertisement = $repository->findOneBy(['id' => $id]);

            if (!$advertisement) {
                throw $this->createNotFoundException('Annonce non trouvée.');
            }
            if ($this->isCsrfTokenValid('delete-item', $request->request->get('token'))) {
                $doctrine->getManager()->remove($advertisement);
                $doctrine->getManager()->flush();

                return $this->redirectToRoute('app_advertisement', [
                    'id' => $id,
                ]);
            }

            return $this->render('advertisement/_form_delete.twig', [
                'id' => $id,
            ]);
        } else {
            return $this->render('advertisement/tu_fais_quoi_la.html.twig', [
                'edit' => true,
            ]);
        }
    }

    private function isAdvertisementOwnerCurrentUser(Advertisement $advertisement, AdvertisementRepository $advertisementRepository): bool
    {
        return $advertisement->getOwner() === $this->getUser();
    }
}
