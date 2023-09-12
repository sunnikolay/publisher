<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Service\Attribute\SubscribedService;

class DefaultController extends AbstractController
{
    #[Route('/')]
    public function root(): Response
    {
        return $this->json(['test' => 'hello']);
    }
}
