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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Contracts\Cache\CacheInterface;

#[Route('/api')]
class UserController extends AbstractFOSRestController
{
    #[Rest\Get(path: '/users', name: 'app_user_list')]
    #[Rest\QueryParam(name: 'keyword', requirements: '\w+', nullable: true, description: 'The fullname of the user to be searched')]
    #[Rest\QueryParam(name: 'order', requirements: 'asc|desc', default: 'asc', description: 'Sort order by user fullname (asc or desc)')]
    #[Rest\QueryParam(name: 'limit', requirements: '\d+', default: '10', description: 'Max number of users per page')]
    #[Rest\QueryParam(name: 'offset', requirements: '\d+', default: '0', description: 'The pagination offset')]
    #[Rest\View(serializerGroups: ['read'])]
    public function list(UserRepository $userRepository, ParamFetcherInterface $paramFetcherInterface, CacheInterface $appCache): iterable
    {
        $client = $this->getUser()->getClient();
        $params = array_values($paramFetcher->all());
        $cacheKey = 'users_' . md5($client->getId() . implode('', $params));

        return $appCache->get($cacheKey, fn() => $userRepository->search($client, ...$params)->getCurrentPageResults());
    }

    #[Rest\Get(path: '/users/{id}', name: 'app_user_show')]
    #[Rest\View(serializerGroups: ['read'])]
    #[Security('is_granted("MANAGE", consumer)', message: 'You are not authorized to access this user')]
    public function show(User $consumer = null): User
    {
        if (!$consumer) {
            throw new NotFoundHttpException('The user you searched for does not exist');
        }

        return $consumer;
    }

    #[Rest\Post(path: '/users', name: 'app_user_create')]
    #[Rest\View(statusCode: 201, serializerGroups: ['read'])]
    #[Security('is_granted("ROLE_ADMIN")', message: 'You are not authorized to create a new user')]
    public function create(User $user, ConstraintViolationListInterface $violations, UserPasswordHasherInterface $hasher): View
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

        $manager = $this->getDoctrine()->getManager();
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
    public function delete(EntityManagerInterface $manager, User $consumer = null): void
    {
        if (!$consumer) {
            throw new NotFoundHttpException('The user you searched for does not exist');
        }

        $manager->remove($consumer);
        $manager->flush();
    }
}
