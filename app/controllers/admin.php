<?php

	#namespace FlotCMS;

	class Admin{

		public static function makeUI($maParams)
		{

			$htmlBody = "";

			$sSection = "items";
			$sAction = "overview";

			$aVarsForView = [];

			# determine section
			switch($maParams['request']->section){
				case 'items':
				case 'elements':
				case 'pictures':
				case 'menus':
				case 'oncologies':
				case 'settings':
				case 'errors':
					$sSection = $maParams['request']->section;
					break;
			}

			# determine actin for section (view all, edit individual etc)
			switch($maParams['request']->action){
				case 'overview':
				case 'new':
				case 'edit':
					$sAction = $maParams['request']->action;
					$aVarsForView['page'] = PageModel::createFromFile($maParams['request']->id);

					//print_r($aVarsForView['page']);
					break;
			}

			switch($sSection){
				case 'items':
					$aItems = PageCollectionModel::getAllItems();
					
					$aReturnItems = [];
					# make a key value array of property name-values with values forced to string
					foreach ($aItems as $iItemId => $oItem) {
						$aPropertyNameValue = [];
						foreach ($oItem as $sPropertyKey => $sPropertyAttributes) {
							$aPropertyNameValue[$sPropertyKey] = $sPropertyAttributes['value'];
						}
						$aReturnItems[$iItemId] = $aPropertyNameValue;
					}

					$aVarsForView['items'] = $aReturnItems;
					break;
			}


			$aVarsForView['section'] = $sSection;
			$aVarsForView['action'] = $sAction;

			return View::render("pages\\admin\\$sSection-$sAction", $aVarsForView);
		}

		public static function handlePost($oRequest)
		{
			$sSection = $oRequest['request']->section;
			$sAction = $oRequest['request']->action;

			if($sSection === "items" && $sAction === "new")
			{
				// create a new page, save it, forward user to editing view
				$oNewPage = PageModel::create();

				$iNewPageId = $oNewPage->save();
				return Helper::Redirect("flot-manage/?section=items&action=edit&id=".$iNewPageId);
			}
			if($sSection === "items" && $sAction === "save")
			{
				// create a new page, save it, forward user to editing view
				$sPageId = Request::get('id');

				if(isset($sPageId))
				{
					$oPageToUpdate = PageModel::createFromFile($sPageId);

					// parse properties

					foreach($oPageToUpdate->aGetAllProperties() as $mKey => $aProperty)
					{
						$mRequestVar = Request::get($mKey, '');

						if($mRequestVar !== null)
						{
							$oPageToUpdate->_SetProperty($mKey, $mRequestVar);
						}
					}


					//print_r($oPageToUpdate);
					$oPageToUpdate->save();
					//print_r($oPageToUpdate);exit();
					$oPageToUpdate->render();
				}

				return Helper::Redirect("flot-manage/?section=items");
			}
		}

		function makeContentsPage($sAction = "list"){

			// depending on the action construct a different content menu

    		

		}

		function logout(){}

		function start()
		{
			// validate user credentials then register the user and do any first run stuff
		}
	}