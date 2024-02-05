<?php

namespace App\Tests\Application\Advertisement\CRUD;

use App\Entity\Advertisement;
use App\Factory\AdvertisementFactory;
use App\Factory\CategoryFactory;
use App\Factory\UserFactory;
use Tests\Support\ApplicationTester;

class CRUDCest
{
    public function AdvertisementWellCreated(ApplicationTester $I): void
    {
        // Login
        UserFactory::createOne(['email' => 'test@test.com', 'roles' => [], 'firstname' => 'test', 'lastname' => 'test', 'isVerified' => 1]);
        $I->amOnPage('/login');
        $I->fillField('#inputEmail', 'test@test.com');
        $I->fillField('#inputPassword', 'test');
        $I->click('.connexion');

        // Next
        CategoryFactory::createOne(['name' => 'test']);
        $I->click('#Create');

        // Remplissez le formulaire avec des données
        $I->fillField('#advertisement_title', 'Annonce test');
        $I->fillField('#advertisement_description', 'ceci est une desciprition de test');
        $I->fillField('#advertisement_price', '5');
        $I->fillField('#advertisement_location', 'Reims');
        $I->selectOption('#advertisement_category', 'test');

        // Envoyez le formulaire
        $I->click('Create');
        $I->see('Annonce test', '.advertisement_name');
    }

    public function unconnectedUserCantCreate(ApplicationTester $I): void
    {
        // On se rend vers la page de création d'une annonce a partir de l'URL
        $I->amOnPage('/advertisement/new');
        // Vu que l'utilisateur n'est pas connecté il est automatiquement redirigé vers la page de connexion
        $I->canSeeInTitle('Log in!');
    }

    // tests
    public function showAdvertisement(ApplicationTester $I)
    {
        $category = CategoryFactory::createOne(['name' => 'test']);
        $user = UserFactory::createOne(['email' => 'test@test.com', 'roles' => [], 'firstname' => 'test', 'lastname' => 'test', 'isVerified' => 1]);
        $adv = AdvertisementFactory::createOne(['category' => $category, 'owner' => $user]);

        $I->amOnPage('/advertisement/'.$adv->getId());
        $I->see($adv->getTitle(), '.advertisement_title');
        $I->see($adv->getDescription(), '.advertisement_description');
        $I->see($adv->getLocation(), '.advertisement_location');
        $I->see($adv->getPrice(), '.advertisement_price');
        $I->see($adv->getOwner()->getFirstname(), '.advertisement_owner');
    }

    public function AdvertisementWellEdited(ApplicationTester $I)
    {
        $category = CategoryFactory::createOne(['name' => 'test']);
        $user = UserFactory::createOne(['email' => 'test@test.com', 'roles' => [], 'firstname' => 'test', 'lastname' => 'test', 'isVerified' => 1]);
        $I->amOnPage('/login');
        $I->fillField('#inputEmail', 'test@test.com');
        $I->fillField('#inputPassword', 'test');
        $I->click('.connexion');
        $adv = AdvertisementFactory::createOne(['category' => $category, 'owner' => $user]);

        $I->amOnPage('/advertisement/'.$adv->getId().'/edit');

        $I->fillField('#advertisement_title', 'Annonce test');
        $I->click('Modifier');
        $I->see('Annonce test', '.advertisement_name');
    }

    public function AdvertisementCannotBeEditedByOtherUser(ApplicationTester $I)
    {
        $category = CategoryFactory::createOne(['name' => 'test']);
        $user1 = UserFactory::createOne(['email' => 'test1@test.com', 'roles' => [], 'firstname' => 'test1', 'lastname' => 'test1', 'isVerified' => 1]);
        UserFactory::createOne(['email' => 'test2@test.com', 'roles' => [], 'firstname' => 'test2', 'lastname' => 'test2', 'isVerified' => 1]);
        $I->amOnPage('/login');
        $I->fillField('#inputEmail', 'test2@test.com');
        $I->fillField('#inputPassword', 'test');
        $I->click('.connexion');

        $adv = AdvertisementFactory::createOne(['category' => $category, 'owner' => $user1]);

        $I->amOnPage('/advertisement/'.$adv->getId().'/edit');
        $I->see('Unauthorized Access', '.unauthorized__title');
    }

    public function AdvertisementWellDeleted(ApplicationTester $I)
    {
        $category = CategoryFactory::createOne(['name' => 'test']);
        $user = UserFactory::createOne(['email' => 'test@test.com', 'roles' => [], 'firstname' => 'test', 'lastname' => 'test', 'isVerified' => 1]);
        $I->amOnPage('/login');
        $I->fillField('#inputEmail', 'test@test.com');
        $I->fillField('#inputPassword', 'test');
        $I->click('.connexion');
        $adv = AdvertisementFactory::createOne(['category' => $category, 'owner' => $user]);
        $title = $adv->getTitle();

        $I->amOnPage('/advertisement/'.$adv->getId());

        $I->click('Delete');
        $I->dontSee($title, '.advertisement_name');
    }

    public function AdvertisementCannotBeDeletedByOtherUser(ApplicationTester $I)
    {
        $category = CategoryFactory::createOne(['name' => 'test']);
        $user1 = UserFactory::createOne(['email' => 'test1@test.com', 'roles' => [], 'firstname' => 'test1', 'lastname' => 'test1', 'isVerified' => 1]);
        UserFactory::createOne(['email' => 'test2@test.com', 'roles' => [], 'firstname' => 'test2', 'lastname' => 'test2', 'isVerified' => 1]);
        $I->amOnPage('/login');
        $I->fillField('#inputEmail', 'test2@test.com');
        $I->fillField('#inputPassword', 'test');
        $I->click('.connexion');

        $adv = AdvertisementFactory::createOne(['category' => $category, 'owner' => $user1]);

        $I->amOnPage('/advertisement/'.$adv->getId());
        $I->dontSee('Delete', '.btnDelete');
    }

    public function AdvertisementWellCreatedByLoggedUser(ApplicationTester $I): void
    {
        // Login
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

        // Fetch advertisement data directly from the database
        $em = $I->grabService('doctrine')->getManager();
        $advertisement = $em->getRepository(Advertisement::class)->findOneBy(['title' => 'Annonce test']);

        $id = $advertisement->getId();
        $I->amOnPage('/advertisement/'.$id);

        $I->see('test', '.advertisement_owner');
    }
}
