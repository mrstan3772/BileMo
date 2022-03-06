<?php

namespace App\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use LogicException;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\MediaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
#[OA\Response(
    response: 405,
    description: 'Method not allowed"',
)]
#[OA\Tag(name: 'Authentication')]
class SecurityController extends AbstractController
{
    #[Rest\Post('/login', name: 'app_login')]
    #[OA\Response(
        response: 200,
        description: 'Returns a JWT token to authenticate the next requests"',
    )]
    #[OA\Response(
        response: 400,
        description: 'JSON data sent invalid"',
    )]
    #[OA\Response(
        response: 401,
        description: 'Invalid credentials"',
    )]
    #[OA\RequestBody(
        new MediaType(
            mediaType: 'application/json',
            schema: new OA\Schema(
                properties: [
                    new OA\Property(
                        property: 'email',
                        description: 'The user\'s email',
                        type: 'string',
                    ),
                    new OA\Property(
                        property: 'password',
                        description: 'The user\'s password',
                        type: 'password',
                    ),
                ]
            ),
        ),
        description: 'User credentials"'
    )]
    public function login(): void
    {
        throw new LogicException('This method can be blank - it will be intercepted by the login key in the firewall');
    }
}
