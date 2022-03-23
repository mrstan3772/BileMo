<?php

namespace App\Controller;

use App\Entity\Phone;
use App\Repository\PhoneRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\Schema;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;

#[Route('/api')]
#[OA\Response(response: 405, description: 'Method not allowed')]
#[OA\Response(response: 401, description: 'Invalid, not found or expired JWT token')]
#[OA\Tag(name: 'Phone')]
class PhoneController extends AbstractFOSRestController
{
    #[Rest\Get(path: '/phones/{id}', name: 'app_phone_show')]
    #[Rest\View(serializerGroups: ['read'])]
    #[OA\Response(
        // new OA\Schema(ref: new Model(type: Phone::class, groups: ['read'])),
        response: 200,
        description: 'Returns the phone according to his id',
    )]
    #[OA\Response(
        response: 404,
        description: 'Phone not found',
    )]
    /**
     * @param  Phone|null $phone
     * 
     * @return Phone
     */
    public function show(Phone $phone = null): Phone
    {
        if (!$phone) {
            throw new NotFoundHttpException('The phone you searched for does not exist');
        }

        return $phone;
    }

    #[Rest\Get(path: '/phones', name: 'app_phone_list')]
    #[Rest\QueryParam(name: 'keyword', requirements: '\w+', nullable: true, description: 'The name of the phone to be searched')]
    #[Rest\QueryParam(name: 'order', requirements: 'asc|desc', default: 'asc', description: 'Sort order by phone name (asc or desc)')]
    #[Rest\QueryParam(name: 'limit', requirements: '\d+', default: '10', description: 'Max number of phones per page')]
    #[Rest\QueryParam(name: 'offset', requirements: '\d+', default: '0', description: 'The pagination offset')]
    #[Rest\View(serializerGroups: ['read'])]
    #[OA\Response(
        // new OA\Schema(ref: new Model(type: Phone::class, groups: ['read'])),
        response: 200,
        description: 'Returns a list of phones',
    )]
    /**
     * @param  PhoneRepository $phoneRepository
     * @param  ParamFetcherInterface $paramFetcher
     * @param  CacheInterface $appCache
     * 
     * @return iterable
     * 
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function list(PhoneRepository $phoneRepository, ParamFetcherInterface $paramFetcher, CacheInterface $appCache): iterable
    {
        $params = array_values($paramFetcher->all());
        $cacheKey = 'phones_' . md5(implode('', $params));

        return $appCache->get($cacheKey, fn () => $phoneRepository->search(...$params)->getCurrentPageResults());
    }
}
