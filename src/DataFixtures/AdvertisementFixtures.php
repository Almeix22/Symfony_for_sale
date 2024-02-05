<?php

namespace App\DataFixtures;

use App\Factory\AdvertisementFactory;
use App\Story\CategoryStory;
use App\Story\UsersStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Zenstruck\Foundry\Factory;

class AdvertisementFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        Factory::delayFlush(function () {
            AdvertisementFactory::createMany(500, function () {
                return ['category' => CategoryStory::getRandom('categories'), 'owner' => UsersStory::getRandom('users')];
            });
        });
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
            UserFixtures::class,
        ];
    }
}
