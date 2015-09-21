<?php

class BaseTest extends PHPUnit_Framework_TestCase
{
    /**
     * A basic functional test example.
     *
     * @return void
     */
     /*
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
    public function testPageModelCreate()
    {
        $oTestPage = PageModel::create();

        $oTestPage->_SetProperty("title", "test page title!");


        $this->assertEquals($oTestPage->mGetProperty('title'), "test page title!");
    }
    public function testPageModelWriteRead()
    {
        // create a page and write and save it, then open it up from disk and make sure attributes are the same, test with funny characters (punctuations and foreign utf8 stuff)

        $oTestPage = PageModel::create();

        $sTestTitle = "did this title save?";

        $oTestPage->_SetProperty("title", $sTestTitle);


        $iIdSaved = $oTestPage->save();

        $oModel = PageModel::createFromFile($iIdSaved);


        $this->assertEquals($oModel->mGetProperty('title'), $sTestTitle);

    }
    public function testPageCollectionCreationModelWriteRead()
    {
        // test that a newly created and saved collection, can be read into memory and contain an empty array of items
        $oPages = PageCollectionModel::create();
        $oPages->save();

        $oSaved = PageCollectionModel::getAllItems();


        $this->assertEquals($oSaved, []);

    }*/
    public function testPageCollectionItemSave()
    {
        // test a new collection by writing an item through it
        $oPages = PageCollectionModel::create();
        $oPages->save();

        $oTestPage = PageModel::create();

        $sTestTitle = "did this title save in the collection?";

        $oTestPage->_SetProperty("title", $sTestTitle);

        PageCollectionModel::saveItem($oTestPage);

        print("\n");
        print("\n");
        print("\n 3 ");
        sleep(0);

        $oSaved = PageCollectionModel::getAllItems();


        print_r($oSaved);


        $this->assertEquals($oSaved[$oTestPage->sUId]['title'], $sTestTitle);

    }
}
