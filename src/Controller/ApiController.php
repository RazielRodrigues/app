<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{

    #[Route('/api/song/{id<\d+>}', name: 'app_api_get_song', methods: 'GET')]
    public function song(int $id)
    {
        # TODO: GET FROM DATABASE
        return $this->json([
            'id' => $id,
            'name' => 'Initial Minimal Techno',
            'url' => 'songs/initial.mp3',
        ], 200);
    }
}
