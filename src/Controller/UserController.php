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
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
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
}
