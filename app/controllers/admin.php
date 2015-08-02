<?php

	class Admin{

		function makeUI($maParams)
		{

			$htmlBody = '';


			$o = new View();

			$htmlBody = "";

			switch($maParams['request']->section){
				case 'items':
					switch($maParams['request']->action){
						case 'edit':
							$htmlBody = "edit buttons<hr/>edit ui";
							break;
						default:
							$htmlBody = "overview buttons<hr/>list??";
							break;
					}
					break;
				default:
					$htmlBody = $maParams['request']->section;
					break;
			}


			return $o->render("admin", ["section" => $maParams['request']->section, "body" => $htmlBody]);
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