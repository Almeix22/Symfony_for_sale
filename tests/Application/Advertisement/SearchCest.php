<?php

namespace App\Tests\Application\Advertisement;

use App\Factory\AdvertisementFactory;
use App\Factory\CategoryFactory;
use Tests\Support\ApplicationTester;

class SearchCest
{
    public function searchTest(ApplicationTester $I)
    {
        $category = CategoryFactory::createMany(1);
        AdvertisementFactory::createMany(5, ['category' => $category[0]]);
        AdvertisementFactory::createOne(['title' => 'Annonce', 'category' => $category[0]]);

        $I->amOnPage('/advertisement');
        $I->seeNumberOfElements('.advertisement_name', 6);
        $I->fillField('#search', 'Annonce');
        $I->click('#rechercher');
        $I->see('Annonce', '.advertisement_name');
        $I->seeNumberOfElements('.advertisement_name', 1);
    }
}
