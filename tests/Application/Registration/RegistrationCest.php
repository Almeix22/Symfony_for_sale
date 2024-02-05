<?php

namespace App\Tests\Application\Registration;

use Tests\Support\ApplicationTester;

class RegistrationCest
{
    public function testSuccessfulRegistration(ApplicationTester $I)
    {
        // Fill the registration form with valid data
        $I->amOnPage('/register');
        $I->fillField('Nom', 'testuser');
        $I->fillField('Prénom', 'testuser');
        $I->fillField('Email', 'test@example.com');
        $I->fillField('Mot de passe', 'TestPassword123!');
        $I->fillField('Répéter votre mot de passe', 'TestPassword123!');
        $I->click('Créer un compte');

        // Check for the success message or redirection to the expected page
        $I->see('Votre compte a été créé avec succès.', 'div.alert.alert-success.custom-success-flash');

        // assert redirection
        $I->seeCurrentRouteIs('app_check_email');
    }

    public function testIncorrectPasswordConfirmation(ApplicationTester $I)
    {
        // Fill the registration form with valid data
        $I->amOnPage('/register');
        $I->fillField('Nom', 'testuser');
        $I->fillField('Prénom', 'testuser');
        $I->fillField('Email', 'test@example.com');
        $I->fillField('Mot de passe', 'TestPassword123!');
        $I->fillField('Répéter votre mot de passe', 'Incorrect Password');
        $I->click('Créer un compte');

        // Check for error messages or stay on the registration page
        $I->see('Les champs de mot de passe doivent correspondre.', 'div.text-accent2.form_register.container > form > div:nth-child(4) > div');
        $I->seeCurrentUrlEquals('/register');
    }

    public function testPasswordComplexityFailure(ApplicationTester $I)
    {
        // Fill the registration form with valid data
        $I->amOnPage('/register');
        $I->fillField('Nom', 'testuser');
        $I->fillField('Prénom', 'testuser');
        $I->fillField('Email', 'test@example.com');
        $I->fillField('Mot de passe', 'weakpassword');
        $I->fillField('Répéter votre mot de passe', 'weakpassword');
        $I->click('Créer un compte');

        // Check for error messages or stay on the registration page
        $I->see('Votre mot de passe doit contenir une minuscule, une majuscule, un chiffre et un caractère spécial et faire 10 caractères minimum.', 'div.text-accent2.form_register.container > form > div:nth-child(4) > div');
        $I->seeCurrentUrlEquals('/register');
    }

    public function testRedirectToEmailConfirmationIfNotVerified(ApplicationTester $I)
    {
        // Fill the registration form with valid data
        $I->amOnPage('/register');
        $I->fillField('Nom', 'testuser');
        $I->fillField('Prénom', 'testuser');
        $I->fillField('Email', 'test@example.com');
        $I->fillField('Mot de passe', 'TestPassword123!');
        $I->fillField('Répéter votre mot de passe', 'TestPassword123!');
        $I->click('Créer un compte');

        // Check for redirection to the email confirmation page
        $I->seeCurrentRouteIs('app_check_email');

        // Attempt to access a protected page without email verification
        $I->amOnPage('/advertisement');
        $I->seeCurrentRouteIs('app_check_email');
        $I->amOnPage('/category');
        $I->seeCurrentRouteIs('app_check_email');
    }

    public function testRegisterAndVerifyEmail(ApplicationTester $I)
    {
        // Fill the registration form with valid data
        $I->amOnPage('/register');
        $I->fillField('Nom', 'testuser');
        $I->fillField('Prénom', 'testuser');
        $I->fillField('Email', 'test@example.com');
        $I->fillField('Mot de passe', 'TestPassword123!');
        $I->fillField('Répéter votre mot de passe', 'TestPassword123!');
        $I->stopFollowingRedirects();
        $I->click('Créer un compte');

        // Extract the email verification link from the confirmation email
        $I->seeEmailIsSent();
        $email = $I->grabLastSentEmail();
        $verificationLink = $this->extractVerificationLink($email->getHtmlBody());

        // Visit the verification link
        $I->amOnPage($verificationLink);

        $I->followRedirect();

        // Check for the expected URL after successful email verification
        $I->seeCurrentUrlEquals('/advertisement');
    }

    // Helper method to extract the verification link from the email content
    protected function extractVerificationLink(string $emailContent): string
    {
        // Use a regular expression to extract the link from the email content
        $pattern = '/href="(.*?)"/';
        preg_match($pattern, $emailContent, $matches);

        return $matches[1] ?? '';
    }
}
