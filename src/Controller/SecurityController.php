<?php

namespace App\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use LogicException;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
#[Rest\Post('/login', name: 'app_login')]
class SecurityController extends AbstractController
{
    
    public function login(): void
    {
        throw new LogicException('This method can be blank - it will be intercepted by the login key in the firewall');
    }
}
