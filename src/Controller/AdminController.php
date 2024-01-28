<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{

    #[Route("/admin", name: "app_admin")]
    public function admin(Request $request, UserRepository $userRepository): Response
    {
        return $this->render("admin/index.html.twig", [
            'data' => $userRepository->findAll()
        ]);
    }

    #[Route("/admin/user/edit", name: "app_admin_user")]
    public function adminUsers(Request $request): Response
    {
        return new Response("fodas");
    }
}
