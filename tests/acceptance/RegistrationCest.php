<?php


namespace App\Tests\acceptance;

use App\Tests\AcceptanceTester;

class RegistrationCest
{
    public function registrationPageWorks(AcceptanceTester $I): void
    {
        $I->amOnPage('/register');
        $I->see('Become our student');
        $I->see('Username');
        $I->seeElement('#registration_form_username');
        $I->seeElement('#registration_form_email');
        $I->seeElement('#registration_form_plainPassword_first');
        $I->seeElement('#registration_form_plainPassword_second');
        $I->seeElement('#registration_form_agreeTerms');
    }

    public function registrationFormWorks(AcceptanceTester $I)
    {
        $I->amOnPage('/register');
        $I->fillField('registration_form[username]', 'katerina');
        $I->fillField('registration_form[email]', 'katerina@yahoo.com');
        $I->fillField('registration_form[plainPassword][first]', '123456');
        $I->fillField('registration_form[plainPassword][second]', '123456');
        $I->checkOption('Agree terms');
        $I->click('Register');
        $I->see('Login');
    }
}
