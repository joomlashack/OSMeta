<?php
use \AcceptanceTester;

class InstallerCest
{
    protected $packagePath = '';

    public function _before(AcceptanceTester $I)
    {
        $I->resizeWindow(1280, 900);

        // Build a new package
        $I->buildProjectUsingPhing();

        // Unzip the built package
        $this->packagePath = $I->unzipBuiltPackageToTmp();

        // Opens the administrator page to login
        $I->amOnPage('/administrator');
        $I->fillField('username', 'admin');
        $I->fillField('passwd', 'admin');
        $I->click('Log in');
    }

    public function _after(AcceptanceTester $I)
    {

    }

    public function installPackage(AcceptanceTester $I)
    {
        // Goes to the installer page
        $I->amOnPage('administrator/index.php?option=com_installer');

        // Click Install from Directory
        $I->click('//*[@id="myTabTabs"]/li[3]/a');

        $I->fillField('install_directory', $this->packagePath);
        $I->click('//*[@id="directory"]/fieldset/div[2]/input');
        $I->makeScreenshot('post_install');

        $I->see('Installing component was successful');
        $I->see('Thanks for');
        $I->see('OSMeta');
        $I->see('Show more details...');
        $I->dontSee('Error');
    }
}
