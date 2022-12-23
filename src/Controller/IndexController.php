<?php

namespace App\Controller;
use App\Repository\AdminUserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class IndexController extends AbstractController
{
    public function __construct(private UrlGeneratorInterface $urlGenerator) {}
    #[Route('/admin', name: 'app_admin_index')]
    public function adminIndex(): Response {
        return $this->render('admin_index.html.twig');
    }
    #[Route('/', name: 'app_index')]
    public function index(): Response {
        return $this->render('index.html.twig');
    }
}