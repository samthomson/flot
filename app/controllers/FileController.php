<?php

	class FileController{

		private static function bSaveFile($sFile, $sFolder, $sContents)
		{
			$sWritePath = $GLOBALS['files.models_path'].$sFile;

			$myfile = fopen($sWritePath, "w") or die("file fail");

			if (flock($myfile,LOCK_EX))
			{
				$sContents = '<?php#'.$sContents.'?>';
				fwrite($myfile, $sContents);
  				flock($myfile,LOCK_UN);
				fclose($myfile);
				return true;
			}
			else{
				return false;
			}
		}

		public static function bTempSaveFile($sFile, $sContents)
		{
			$sFile = fopen($sFile, "w") or die("file fail");
			
			if (flock($sFile,LOCK_EX))
			{
				fwrite($sFile, $sContents);
  				flock($sFile,LOCK_UN);
				fclose($sFile);
				return true;
			}
			else{
				return false;
			}
		}

		public static function sReadTextFromFile($sFilePath)
		{
			
			$sText = '';

			$fModel = @fopen($sFilePath, "r");

			clearstatcache(true, $sFilePath);
			$sText = @fread($fModel, filesize($sFilePath));

			@fclose($fModel);

			# remove opening php
			if(strlen($sText) > 5)
				$sText = substr($sText, 6);

			# remove closing php
			if(strlen($sText) > 2)
				$sText = substr($sText, 0, strlen($sText)-2);

			return $sText;
		}

		public static function bSaveModel($sFile, $sContents)
		{
			return self::bSaveFile("item_".$sFile.".flotcms", "datastore", $sContents);
		}

		public static function bSaveCollection($sCollectionName, $sContents)
		{
			return self::bSaveFile("collection_".$sCollectionName.".flotcms", "datastore", $sContents);
		}

		public static function getModels()
		{
			$sReadPath = $GLOBALS['files.models_path'];

			$sScanPath = $sReadPath."*.flotcms";

			$aObjects = [];

			foreach (glob($sScanPath) as $sModelPath)
			{
				$fModel = fopen($sModelPath, "r") or die("can't read model file");

				$oParsed = PageModel::createFromJson(fread($fModel, filesize($sModelPath)));

				array_push($aObjects, $oParsed);
				fclose($fModel);
			}

			return $aObjects;
		}
	}