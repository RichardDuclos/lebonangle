<?php

namespace App\Controller;

use App\Entity\Advert;
use App\Form\AdvertType;
use App\Repository\AdvertRepository;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Workflow\WorkflowInterface;

#[Route('/advert')]
class AdvertController extends AbstractController
{
    public function __construct(private UrlGeneratorInterface $urlGenerator, private WorkflowInterface $advertPublishingStateMachine) {}
    #[Route('/', name: 'app_advert_index', methods: ['GET'])]
    public function index(Request $request, AdvertRepository $advertRepository): Response
    {
        $queryBuilder = $advertRepository
            ->createQueryBuilder('advert')
            ->addOrderBy('advert.createdAt', 'DESC');
        $pager = new Pagerfanta(new QueryAdapter($queryBuilder));
        $pager->setMaxPerPage(30);
        $pager->setCurrentPage($request->get('page', 1));
        return $this->render('advert/index.html.twig', [
            'pager' => $pager,
        ]);
    }

    #[Route('/{id}', name: 'app_advert_show', methods: ['GET'])]
    public function show(Advert $advert): Response
    {
        return $this->render('advert/show.html.twig', [
            'advert' => $advert,
        ]);
    }


    #[Route('/{id}/publish', name: 'app_advert_publish', methods: ['GET'])]
    public function publish(Request $request, Advert $advert, AdvertRepository $advertRepository): Response{
        if ($this->advertPublishingStateMachine->can($advert, 'publish')) {
            $this->advertPublishingStateMachine->apply($advert, 'publish');
        }
        $advert->setPublishedAt(new \DateTimeImmutable());
        $advertRepository->save($advert, true);
        $this->addFlash('publish_state', 'Annonce publiée');
        return new RedirectResponse($this->urlGenerator->generate('app_advert_index', ['id' => $advert->getId()]));
    }

    #[Route('/{id}/unpublish', name: 'app_advert_unpublish', methods: ['GET'])]
    public function unpublish(Request $request, Advert $advert, AdvertRepository $advertRepository): Response{
        if ($this->advertPublishingStateMachine->can($advert, 'unpublish')) {
            $this->advertPublishingStateMachine->apply($advert, 'unpublish');
        }
        $advertRepository->save($advert, true);
        $this->addFlash('publish_state', 'Annonce annulée');
        return new RedirectResponse($this->urlGenerator->generate('app_advert_index', ['id' => $advert->getId()]));
    }

    #[Route('/{id}/reject', name: 'app_advert_reject', methods: ['GET'])]
    public function reject(Request $request, Advert $advert, AdvertRepository $advertRepository): Response{
        if ($this->advertPublishingStateMachine->can($advert, 'reject')) {
            $this->advertPublishingStateMachine->apply($advert, 'reject');
        }
        $advertRepository->save($advert, true);
        $this->addFlash('publish_state', 'Annonce rejetée');
        return new RedirectResponse($this->urlGenerator->generate('app_advert_index', ['id' => $advert->getId()]));
    }
}
