<?php

namespace App\Tests\Application\Advertisement\List;

use App\Factory\AdvertisementFactory;
use App\Factory\CategoryFactory;
use Tests\Support\ApplicationTester;

class ListCest
{
    public function adversitementListCorrectlyShown(ApplicationTester $I): void
    {
        $I->amOnPage('/advertisement');
        $I->seeElement('.no_add');
    }

    public function adversitementListWithPagination(ApplicationTester $I): void
    {
        $categorie = CategoryFactory::createMany(1);
        AdvertisementFactory::createMany(20, ['category' => $categorie[0]]);
        $I->amOnPage('/advertisement');
        $I->seeNumberOfElements('.list-group-item', 15);
        $I->seeElement('.pagination');
    }
}
