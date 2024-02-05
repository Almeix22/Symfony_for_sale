<?php

namespace App\Story;

use App\Factory\AdvertisementFactory;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Story;

final class UsersStory extends Story
{
    public function build(): void
    {
        $this->addToPool('users', UserFactory::createOne(['email' => 'admin@example.com', 'roles' => ['ROLE_ADMIN'], 'isVerified' => 1]));
        $this->addToPool('users', UserFactory::createOne(['email' => 'admin2@example.com', 'roles' => ['ROLE_ADMIN'], 'isVerified' => 1]));
        $this->addToPool('users', UserFactory::createOne(['email' => 'user2@example.com', 'roles' => ['ROLE_USER'], 'isVerified' => 1]));

        $this->addToPool('users', UserFactory::createMany(10, ['isVerified' => 1]));

        $this->addToPool('users', UserFactory::createOne(['email' => 'user@example.com', 'roles' => ['ROLE_USER'], 'isVerified' => 1, 'advertisements' => AdvertisementFactory::createMany(20, ['category' => CategoryStory::getRandom('categories')])]));
        $this->addToPool('unverified users', UserFactory::createMany(4));
    }
}
