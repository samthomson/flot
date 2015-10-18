<?php

class AdminSectionUITest extends PHPUnit_Framework_TestCase
{
    /**
     * A basic functional test example.
     *
     * @return void
     */

    public function testAdminPagesReachableVisible()
    {
        $driver = new \Behat\Mink\Driver\GoutteDriver();
        $session = new \Behat\Mink\Session($driver);

        $saPages = [
            'items' => '',
            'elements' => '?section=elements',
            'pictures' => '?section=pictures',
            'menus' => '?section=menus',
            'oncologies' => '?section=oncologies',
            'settings' => '?section=settings',
            'errors' => '?section=errors'
        ];

        $session->start();

        $bResults = [];

        foreach ($saPages as $sClass => $sPageEnd) {
            $session->visit('http://flot1.dev/flot-manage/'.$sPageEnd);
            $page = $session->getPage();

            $sectionLink = $page->find('css', '.'.$sClass.'.admin_menu_left.active');

            array_push($bResults, ($sectionLink === null ? false : true));
        }

        $this->assertNotContains(false, $bResults);
    }

}
