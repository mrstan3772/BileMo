<?php


namespace App\DataFixtures\Providers;

class ClientProvider
{
    /**
     * Contains all client names
     *
     * @var array
     */
    private const CLIENTS = [
        'LDLC',
        'Materiel.net',
        'Amazon',
        'Cybertek',
        'Cdiscount',
        'Boulanger',
        'Darty',
        'Fnac',
        'Rakuten',
        'Goboo',
        'AliExpress'
    ];

    /**
     * @return array
     */
    public static function clients(): array
    {
        return static::CLIENTS;
    }
}
