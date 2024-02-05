<?php

namespace App\Tests\Application\Home;

use Tests\Support\ApplicationTester;

class HomeCest
{
    public function index(ApplicationTester $I): void
    {
        $I->amOnPage('/');
        $I->canSeePageRedirectsTo('/', '/advertisement');
    }
}
