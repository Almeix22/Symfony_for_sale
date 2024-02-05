<?php

namespace App\DataFixtures;

use App\Story\UsersStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Zenstruck\Foundry\Factory;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        Factory::delayFlush(function () {
            UsersStory::load();
        });
    }
}
