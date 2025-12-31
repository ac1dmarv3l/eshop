<?php

declare(strict_types=1);

namespace App\Common\Application\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DefaultController extends AbstractController
{
    #[Route('/', name: 'app_default')]
    public function __invoke(): Response
    {
        return $this->render('default/index.html.twig', []);
    }
}
