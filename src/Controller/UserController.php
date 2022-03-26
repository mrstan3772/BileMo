<?php

namespace App\Controller;

use App\Entity\User;
use App\Exception\ResourceValidationException;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\MediaType;
use OpenApi\Attributes\Schema;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Contracts\Cache\CacheInterface;

#[Route('/api')]
#[OA\Response(response: 405, description: 'Method not allowed')]
#[OA\Response(response: 401, description: 'Invalid, not found or expired JWT token')]
#[OA\Tag(name: 'User')]
class UserController extends AbstractFOSRestController
{
    #[Rest\Get(path: '/users', name: 'app_user_list')]
    #[Rest\QueryParam(name: 'keyword', requirements: '\w+', nullable: true, description: 'The fullname of the user to be searched')]
    #[Rest\QueryParam(name: 'order', requirements: 'asc|desc', default: 'asc', description: 'Sort order by user fullname (asc or desc)')]
    #[Rest\QueryParam(name: 'limit', requirements: '\d+', default: '10', description: 'Max number of users per page')]
    #[Rest\QueryParam(name: 'offset', requirements: '\d+', default: '0', description: 'The pagination offset')]
    #[Rest\View(serializerGroups: ['read'])]
    #[OA\Response(
        // new OA\Schema(ref: new Model(type: User::class, groups: ['read'])),
        response: 200,
        description: 'Returns a list of users according to the client id',
    )]
    /**
     * @param  UserRepository $userRepository
     * @param  ParamFetcherInterface $paramFetcherInterface
     * @param  CacheInterface $appCache
     * 
     * @return iterable
     * 
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function list(UserRepository $userRepository, ParamFetcherInterface $paramFetcher, CacheInterface $appCache): iterable
    {
        $client = $this->getUser()->getClient();
        $params = array_values($paramFetcher->all());
        $cacheKey = 'users_' . md5($client->getId() . implode('', $params));

        return $appCache->get($cacheKey, fn () => $userRepository->search($client, ...$params)->getCurrentPageResults());
    }

    #[Rest\Get(path: '/users/{id}', name: 'app_user_show')]
    #[Rest\View(serializerGroups: ['read'])]
    #[Security('is_granted("MANAGE", consumer)', message: 'You are not authorized to access this user')]
    #[OA\Response(
        // new OA\Schema(ref: new Model(type: User::class, groups: ['read'])),
        response: 200,
        description: 'Returns the user according to his id',
    )]
    #[OA\Response(
        response: 404,
        description: 'User not found',
    )]
    #[OA\Response(
        response: 400,
        description: 'Bad Request',
    )]
    /**
     * @param  User|null $consumer
     * 
     * @return User
     */
    public function show(Request $request, User $consumer = null): User
    {
        if (!is_numeric($request->get('id'))) {
            throw new BadRequestHttpException('Invalid type, the value must to be a number');
        }

        if (!$consumer) {
            throw new NotFoundHttpException('The user you searched for does not exist');
        }

        return $consumer;
    }

    #[Rest\Post(path: '/users', name: 'app_user_create')]
    #[Rest\View(statusCode: 201, serializerGroups: ['read'])]
    #[Security('is_granted("ROLE_ADMIN")', message: 'You are not authorized to create a new user')]
    #[ParamConverter(
        'user',
        class: 'App\Entity\User',
        converter: 'fos_rest.request_body',
        options: [
            'validator' => ['groups' => 'create'],
            'deserializationContext' => ['groups' => ['create']]
        ]
    )]
    #[OA\Response(
        // new OA\Schema(ref: new Model(type: User::class, groups: ['read'])),
        response: 201,
        description: 'Returns the user added',
    )]
    #[OA\Response(
        response: 403,
        description: 'Insufficient rights to create a user',
    )]
    #[OA\Response(
        response: 400,
        description: 'Malformed JSON or constraint validation errors',
    )]
    #[OA\RequestBody(
        description: 'User information',
        content: [
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
                            type: 'string',
                            format: 'password'
                        ),
                        new OA\Property(
                            property: 'phoneNumber',
                            description: 'The user\'s full phone number',
                            type: 'int',
                        ),
                        new OA\Property(
                            property: 'fullname',
                            description: 'The user\'s full name',
                            type: 'string',
                        ),
                        new OA\Property(
                            property: 'roles',
                            description: 'The user\'s full roles',
                            type: 'array',
                            items: new OA\Items(
                                type: 'string',
                                title: 'role'
                            )
                        ),
                    ]
                ),
            )
        ]
    )]
    public function create(EntityManagerInterface $manager, User $user, ConstraintViolationListInterface $violations, UserPasswordHasherInterface $hasher): View
    {
        if (count($violations)) {
            $message = 'The JSON sent contains invalid data: ';
            foreach ($violations as $violation) {
                /** @var ConstraintViolationInterface $violation */
                $message .= sprintf(
                    'Field %s: %s; ',
                    $violation->getPropertyPath(),
                    $violation->getMessage()
                );
            }

            throw new ResourceValidationException($message);
        }

        $user->setClient($this->getUser()->getClient());
        $user->setPassword($hasher->hashPassword($user, $user->getPassword()))
            ->setCreatedAt(new \DateTime());

        $manager->persist($user);
        $manager->flush();

        return $this->view(
            $user,
            Response::HTTP_CREATED,
            ['location' => $this->generateUrl('app_user_show', ['id' => $user->getId()], UrlGeneratorInterface::ABSOLUTE_URL)]
        );
    }

    #[Rest\Delete(path: '/users/{id}', name: 'app_user_delete', requirements: ['id' => '\d+'])]
    #[Rest\View(statusCode: 204)]
    #[Security('is_granted("ROLE_ADMIN") and is_granted("MANAGE", consumer)', message: 'You are not authorized to create a new user')]
    #[OA\Response(
        response: 204,
        description: 'No content',
    )]
    #[OA\Response(
        response: 404,
        description: 'User not found',
    )]
    #[OA\Response(
        response: 403,
        description: 'Different common client or insufficient rights to delete a user',
    )]
    #[OA\Response(
        response: 400,
        description: 'Bad Request',
    )]
    public function delete(Request $request, EntityManagerInterface $manager, User $consumer = null): void
    {

        if (!is_numeric($request->get('id'))) {
            throw new BadRequestHttpException('Invalid type, the value must to be a number');
        }

        if (!$consumer) {
            throw new NotFoundHttpException('The user you searched for does not exist');
        }

        $manager->remove($consumer);
        $manager->flush();
    }
}
