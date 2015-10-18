<?php

class FileSystemTest extends PHPUnit_Framework_TestCase
{
    /**
     * A basic functional test example.
     *
     * @return void
     */
    
    public function testPageModelCreate()
    {
        $oTestPage = PageModel::create();

        $oTestPage->_SetProperty("title", "test page title!");


        $this->assertEquals($oTestPage->mGetProperty('title'), "test page title!");
    }
    public function testPageModelPublishableCreate()
    {
        $aExpectedStates = [false, true, false, true];
        $aActualStates = [];

        $oTestPage = PageModel::create();

        array_push($aActualStates, $oTestPage->mGetProperty('published'));

        $oTestPage->_SetProperty("published", true);

        array_push($aActualStates, $oTestPage->mGetProperty('published'));

        $oTestPage->_SetProperty("published", false);

        array_push($aActualStates, $oTestPage->mGetProperty('published'));
        $oTestPage->_SetProperty("published", true);

        array_push($aActualStates, $oTestPage->mGetProperty('published'));


        $this->assertEquals($aExpectedStates, $aActualStates);
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

    }
    public function testPageCollectionItemSave()
    {
        // test a new collection by writing an item through it
        $oPages = PageCollectionModel::create();
        $oPages->save();

        $oTestPage = PageModel::create();

        $aProperties = [
            "title" => "did this title save in the collection?",
            "published" => true
        ];

        $sTestTitle = "did this title save in the collection?";

        foreach ($aProperties as $sKey => $sValue) {
            $oTestPage->_SetProperty($sKey, $sValue);
        }
        

        PageCollectionModel::updateItem($oTestPage);

        $oSaved = PageCollectionModel::getAllItems();

        $aMatches = [];

        foreach ($aProperties as $sKey => $sValue) {
            array_push($aMatches, ($oSaved[$oTestPage->sUId][$sKey] === $sValue ? true : false));
        }

        $this->assertNotContains(false, $aMatches);
    }
}
