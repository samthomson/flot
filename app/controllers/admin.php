<?php

	#namespace FlotCMS;

	class Admin{

		public static function makeUI($maParams)
		{

			$htmlBody = "";

			$sSection = "items";
			$sAction = "overview";


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

			switch($maParams['request']->action){
				case 'overview':
				case 'new':
				case 'edit':
					$sAction = $maParams['request']->action;
					break;
			}

			/*

			switch($maParams['request']->section){
				case 'items':
					switch($maParams['request']->action){
						case 'new':
							$htmlBody .= "edit buttons";
							$htmlBody .= "<hr/>";
							$htmlBody .= "edit ui";
							break;
					}
					break;
				default:
					$htmlBody = $maParams['request']->section;
					break;
			}
			*/

			return View::render("pages\\admin\\$sSection-$sAction", ['section' => $sSection]);
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