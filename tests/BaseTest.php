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

        $this->assertEquals($session->getStatusCode(),200);
        //$this->visit('/')->see('[to be generated]');
    }
}
