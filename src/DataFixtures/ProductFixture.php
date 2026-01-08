<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Product\Domain\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class ProductFixture extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        $product = Product::create(
            'Large olive wood serving board',
            'Large olive wood serving board – natural shape with handle – handcrafted unique piece – antipasti & presentation board',
            '146.57',
            'https://i.etsystatic.com/35136815/r/il/c26e16/7501657946/il_1140xN.7501657946_ic4f.jpg',
        );
        $manager->persist($product);

        $product = Product::create(
            'Serving Tray Wood',
            'Serving Tray Wood, Serving Tray Personalized, Engraved Serving tray, Personalized Platter, Wooden Tray, Custom Tray, Housewarming Gift',
            '70.00',
            'https://i.etsystatic.com/33269300/r/il/d6cd9f/5552264807/il_1140xN.5552264807_pzdz.jpg',
        );
        $manager->persist($product);

        $product = Product::create(
            'Engraved Charcuterie Board Set',
            '5th Anniversary Gift for Couple Entertainer’s Bundle Engraved Charcuterie Board Set with Knives and Coasters',
            '293.10',
            'https://i.etsystatic.com/13596232/r/il/1effc9/7375875437/il_1140xN.7375875437_ql5y.jpg',
        );
        $manager->persist($product);

        $product = Product::create(
            '5th Anniversary Gift for Couple',
            '5th Anniversary Gift for Couple, Personalised Resin Serving Board, Handmade Engraved Cheese Board, Unique Kitchen Gift',
            '103.99',
            'https://i.etsystatic.com/13596232/r/il/2c2087/7391305467/il_1140xN.7391305467_67u8.jpg',
        );
        $manager->persist($product);

        $product = Product::create(
            'Personalised Resin Cheese Board',
            '5th Anniversary Gift for Couple, Personalised Resin Cheese Board, Engraved Wooden Chopping Board, Handmade Serving Board, Custom Home Gift',
            '110.97',
            'https://i.etsystatic.com/13596232/r/il/44bad9/7390579661/il_1140xN.7390579661_444r.jpg',
        );
        $manager->persist($product);

        $product = Product::create(
            'Live edge walnut cutting board',
            'Live edge walnut cutting board, epoxy handle rustic cheese platter',
            '124.93',
            'https://i.etsystatic.com/50197545/r/il/b4baff/7217854531/il_1140xN.7217854531_ksc3.jpg',
        );
        $manager->persist($product);

        $product = Product::create(
            'Black Epoxy and Wood Serving Tray',
            'Black Epoxy and Wood Serving Tray – Handmade Resin River Tray with Handles',
            '225.00',
            'https://i.etsystatic.com/59980959/r/il/b7915b/7044590879/il_1140xN.7044590879_1m6y.jpg',
        );
        $manager->persist($product);
        $manager->flush();
    }
}
