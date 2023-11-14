<?php


namespace App\Tests\acceptance;

use App\Tests\AcceptanceTester;

class SigninCest
{
    public function loginPageWorks(AcceptanceTester $I)
    {
        $I->amOnPage('/login');
        $I->see('Please enter your credentials to login');
        $I->see('Username');
        $I->see('Password');
        $I->seeElement('#username');
        $I->seeElement('#password');
        $I->seeElement('button[type="submit"]');
    }

    public function loginActionWorks(AcceptanceTester $I)
    {
        $I->amOnPage('/login');
        $I->fillField('_username', 'victoria');
        $I->fillField('_password', '123456');
        $I->see('ABOUT US');
        $I->click('button[type="submit"]');
        $I->amOnPage('/');
    }
}
