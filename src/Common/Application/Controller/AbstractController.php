<?php

declare(strict_types=1);

namespace App\Common\Application\Controller;

use \Symfony\Bundle\FrameworkBundle\Controller\AbstractController as SymfonyAbstractController;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractController extends SymfonyAbstractController
{
    final protected function singleObjectResponse(?array $data, int $status): Response
    {
        $data['result'] = true;

        return $this->json(
            $data,
            $status,
        );
    }
}
