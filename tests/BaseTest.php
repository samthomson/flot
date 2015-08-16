<?php

class BaseTest extends PHPUnit_Framework_TestCase
{
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testBasicExample()
    {
        //$this->assertEquals(1,1);

        $driver = new \Behat\Mink\Driver\GoutteDriver();
        $session = new \Behat\Mink\Session($driver);

        $session->start();
        $session->visit('http://flot1.dev/');
        $page = $session->getPage();

        //$this->assertEquals($session->getStatusCode(),200);
        $this->assertEquals($page->getText(),'[to be generated]');
        //$this->visit('/')->see('[to be generated]');
    }
}
