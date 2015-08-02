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

			$htmlBody = "";

			switch($sAction){
				case 'edit':
					$htmlBody = "edit buttons<hr/>edit ui";
					break;
				default:
					$htmlBody = "overview buttons<hr/>list of items?";
					break;
			}

    		return $o->render("admin", ["section" => "items", "body" => $htmlBody]);

		}

		function logout(){}

		function start()
		{
			// validate user credentials then register the user and do any first run stuff
		}
	}