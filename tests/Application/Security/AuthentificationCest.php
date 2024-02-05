<?php

namespace Tests\Application\Security;

use App\Factory\UserFactory;
use Tests\Support\ApplicationTester;

class AuthentificationCest
{
    public function index(ApplicationTester $I): void
    {
        UserFactory::createOne(['email' => 'test@test.com', 'roles' => [], 'firstname' => 'test', 'lastname' => 'test']);
        $I->amOnPage('/login');
        $I->fillField('#inputEmail', 'test@test.com');
        $I->fillField('#inputPassword', 'test');
        $I->click('Connexion');
        $I->seeInCurrentUrl('/');
    }
}
