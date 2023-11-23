<?php


namespace App\Tests\acceptance;

use App\Tests\AcceptanceTester;

class CourseCest
{
    public function _before(AcceptanceTester $I)
    {
        $I->amOnPage('/login');
        $I->fillField('_username', 'victoria');
        $I->fillField('_password', '123456');
        $I->click('button[type="submit"]');
        $I->see('Complete Online Courses');
    }

    public function createCourse(AcceptanceTester $I)
    {
        $I->amOnPage('/courses/');
        $I->seeElement(['class' => 'mt-3.btn.btn-primary']);
        $I->click('Create new');
        $I->amOnPage('/courses/new');
        $I->fillField('courses[title]', 'API in PHP: From basic to advanced');
        $I->fillField('courses[description]', 'The purpose of this course is to help you learn and understand how to create a RESTful API using PHP');
        $I->fillField('courses[hours]', '7');
        $I->fillField('courses[minutes]', '27');
        $I->selectOption('Instructor', 'victoria');
        $I->seeElement(['class' => 'btn.btn-success']);
        $I->click('Save');

        $I->see('API in PHP: From basic to advanced');
    }
    public function deleteCourse(AcceptanceTester $I)
    {
        $I->amOnPage('/courses/');
        $I->see('Social Media Course');
        $I->seeElement(['class' => 'btn.btn-primary.btn-sm.mt-2']);
        $I->click('Edit');
        $I->amOnPage('/courses/3/edit');
        $I->see('Title');
        $I->see('Description');
        $I->seeElement(['class' => 'btn.btn-danger']);
        $I->click('Delete'); // Adjust the selector based on your HTML structure

        // Assert that the course is deleted
        $I->dontSee('Social Media Course');
    }
}
