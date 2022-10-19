<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Flex\Options;

class MainController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render(view:'home/index.html.twig');
    }

    /**
     * @Route("/custom/{name?}", name="custom")
     */
    public function custom(Request $request): Response
    {
        $name = $request->get(key:'name');
        return $this->render('home/custom.html.twig', [
            'name' => $name
        ]);
    }
}
