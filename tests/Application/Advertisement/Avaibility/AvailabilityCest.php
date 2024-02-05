<?php

namespace App\Tests\Application\Advertisement\Avaibility;

use App\Factory\CategoryFactory;
use App\Factory\UserFactory;
use Codeception\Attribute\DataProvider;
use Codeception\Attribute\Group;
use Codeception\Example;
use Tests\Support\ApplicationTester;

class AvailabilityCest
{
    /**
     * @dataProvider pageProvider
     */
    #[Group('available')] // set a group for this test
    public function pageIsAvailable(ApplicationTester $I, Example $data)
    {
        $url = $data['url'];
        $I->amOnPage($url);
        $I->seeResponseCodeIs(200);
    }

    public function pageProvider(): array
    {
        return [
            ['url' => '/advertisement'],
            ['url' => '/category'],
            ['url' => '/advertisement/new'],
            ['url' => '/login'],
            ['url' => '/register'],
            ['url' => '/verify/email'],
            ['url' => '/check/email'],
        ];
    }

    public function isMyAdvertisements(ApplicationTester $I)
    {
        UserFactory::createOne(['email' => 'test@test.com', 'roles' => [], 'firstname' => 'test', 'lastname' => 'test', 'isVerified' => 1]);
        $I->amOnPage('/login');
        $I->fillField('#inputEmail', 'test@test.com');
        $I->fillField('#inputPassword', 'test');
        $I->click('.connexion');

        // Next
        CategoryFactory::createOne(['name' => 'test']);
        $I->click('#Create');

        // Fill the form with data
        $I->fillField('#advertisement_title', 'Annonce test');
        $I->fillField('#advertisement_description', 'ceci est une desciprition de test');
        $I->fillField('#advertisement_price', '5');
        $I->fillField('#advertisement_location', 'Reims');
        $I->selectOption('#advertisement_category', 'test');

        // Submit the form
        $I->click('Create');
        $I->see('Annonce test', '.advertisement_name');

        $I->click('#MyAdvertisements');

        $I->click('#Advertisement');

        // Assert that the owner of the advertisement is the logged-in user
        $I->see('test', '.advertisement_owner');
    }
}
