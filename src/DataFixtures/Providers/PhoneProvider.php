<?php


namespace App\DataFixtures\Providers;

class PhoneProvider
{
    /**
     * Contains all phones sorted by brand
     *
     * @var array[]
     */
    private const PHONES = [
        'Apple'   => [
            'Iphone 11',
            'Iphone SE 2',
            'Iphone 12',
            'Iphone 12 Mini',
            'Iphone 13',
            'Iphone 13 Mini',
            'iPhone 13 Pro',
            'iPhone 13 Pro Max'
        ],
        'Google'  => [
            'Pixel 6',
            'Pixel 6 Pro',
            'Pixel 5a 5G',
            'Pixel 5',
            'Pixel 4a 5G',
            'Pixel 4a',
            'Pixel 4 XL',
            'Pixel 4',
            'Pixel 3a XL',
            'Pixel 3a',
        ],
        'Xiaomi'  => [
            'Redmi Note 11 4G',
            'Redmi Note 11 Pro 4G',
            'Redmi Note 11 Pro 5G',
            'Redmi Note 11S',
        ],
        'Samsung' => [
            'Galaxy S22',
            'Galaxy M23',
            'Galaxy A13',
        ],
        'ZTE'     => [
            'Blade A3',
            'Blade A5',
            'Blade A7',
        ],
        'Acer' => [
            'Predator 8',
            'Liquid X2',
        ],
        'Motorola'   => [
            'Moto G22',
            'Edge 30 Pro',
        ],
        'Huawei'  => [
            'P50 Pocket',
            'Enjoy 20e',
            'Watch GT Runner',
        ],
        'LG'      => [
            'W41 Pro',
            'W41',
            'K52',
            'K62',
        ],
    ];

    /**
     * @return array
     */
    public static function phonesSortedByBrand(): array
    {
        return static::PHONES;
    }
}
