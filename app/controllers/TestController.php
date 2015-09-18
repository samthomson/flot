<?php

	use Carbon\Carbon as Carbon;

	class TestController extends BaseController{

		public static function test()
		{
			return "static test?";
		}
		public static function makeItem()
		{
			// make an item
			$oTestPage = PageModel::create();

			$oTestPage->_SetProperty("title", "test page title!");

			//echo $oTestPage->mGetProperty("title");

			$oTestPage->save();
		}
	}