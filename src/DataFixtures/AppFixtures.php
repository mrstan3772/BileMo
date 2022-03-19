<?php

namespace App\DataFixtures;

use App\DataFixtures\Providers\ClientProvider;
use App\DataFixtures\Providers\PhoneProvider;
use App\Entity\User;
use App\Factory\ClientFactory;
use App\Factory\PhoneFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        PhoneFactory::faker()->addProvider(new PhoneProvider);
        ClientFactory::faker()->addProvider(new ClientProvider);

        $phones = PhoneFactory::faker()->phonesSortedByBrand();
        foreach ($phones as $brand => $phones) {
            foreach ($phones as $phone) {
                PhoneFactory::createOne(
                    [
                        'name' => $phone,
                        'brand' => $brand
                    ]
                );
            }
        }

        $clients = ClientFactory::faker()->clients();
        $created_clients = [];
        foreach ($clients as $client) {
            $created_clients[] = ClientFactory::createOne(
                [
                    'name' => $client
                ]
            );
        }

        foreach ($created_clients as $client) {
            UserFactory::createOne(
                [
                    'client' => $client
                ]
            );
        }

        $manager->flush();
    }
}
