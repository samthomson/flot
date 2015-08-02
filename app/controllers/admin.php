<?php

	class Admin{

		function makeUI($maParams)
		{
			switch ($maParams['request']->section) {
				case 'contents':
					self::makeContentsPage($maParams['request']->action);
					break;
				
				default:
					self::makeContentsPage($maParams['request']->action);
					break;
			}
		}

		function makeContentsPage($sAction = "list"){

			// depending on the action construct a different content menu

			$o = new View();

    		return $o->render("admin", ["section" => "items"]);

		}

		function logout(){}

		function start()
		{
			// validate user credentials then register the user and do any first run stuff
		}
	}