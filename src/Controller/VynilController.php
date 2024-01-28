<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class VynilController extends AbstractController
{
    #[Route("/play", name: 'app_play')]
    public function homepage()
    {

        $tracks = [
            ['song' => 'Initial Minimal Techno', 'artist' => 'Raziel Rodrigues'],
        ];

        return $this->render(
            "vynyl/homepage.html.twig",
            ['tracks' => $tracks]
        );
    }

    #[Route("/browse/{genre}", name: 'app_browse')]
    public function browse(string $genre = null)
    {
        if ($genre) {
            $genre = str_replace('-', ' ', $genre);
        } else {
            $genre = "All genres";
        }

        return $this->render("vynyl/browse.html.twig", [
            'genre' => $genre
        ]);
    }
}
