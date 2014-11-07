<?php
use \AcceptanceTester;

class ArticleCest
{
    public function _before(AcceptanceTester $I)
    {
        $I->resizeWindow(1280, 900);

        // Opens the administrator page to login
        $I->amOnPage('/administrator');
        $I->fillField('username', 'admin');
        $I->fillField('passwd', 'admin');
        $I->click('Log in');
    }

    public function _after(AcceptanceTester $I)
    {

    }

    public function changeArticleMetaData(AcceptanceTester $I)
    {
        $uniqid = uniqid();
        $customTitle = "Custom title $uniqid";
        $customDescription = "Custom description $uniqid";

        // Goes to the OSMeta page
        $I->amOnPage('administrator/index.php?option=com_osmeta');

        // Set a custom meta title
        $I->fillField('//*[@id="articleList"]/tbody/tr[5]/td[3]/textarea', $customTitle);
        $I->fillField('//*[@id="articleList"]/tbody/tr[5]/td[4]/textarea', $customDescription);

        // Save
        $I->click('//*[@id="toolbar-apply"]/button');

        // Load the article on frontend
        $I->amOnPage('8-category1/1-test');

        // Check title
        $I->seeInTitle($customTitle);
        $I->seeInPageSource("<meta name=\"title\" content=\"{$customTitle}\">");
        $I->seeInPageSource("<meta name=\"metatitle\" content=\"{$customTitle}\">");

        // Check description
        $I->seeInPageSource("<meta name=\"description\" content=\"{$customDescription}\">");

        // General errors
        $I->dontSee('Error');
    }
}
