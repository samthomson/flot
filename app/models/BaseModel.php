<?php

	class BaseModel
	{
		protected $amProperties = [];
		protected $sType = "";
		public $sUId = "";

		public function __construct() {
			$this->sUId = uniqid(16);
		}


		public static function create()
		{
			return new static();
		}

		public static function createFromFile($sUId)
		{
			$sReadPath = $GLOBALS['files.models_path'];

			$sFilePath = $sReadPath."item_$sUId.flotcms";

			$fModel = fopen($sFilePath, "r") or die("can't read model file");

			$oParsed = self::createFromJson(fread($fModel, filesize($sFilePath)));

			fclose($fModel);

			return $oParsed;
		}
		public static function createFromJson($sJson)
		{
			// parse json and return a new object accordingly
			$o = new static();

			$oJson = json_decode($sJson);

			// object level properties
			$o->sUId = $oJson->sUId;

			// cms level properties
			if(isset($oJson->amProperties))
			{
				foreach ($oJson->amProperties as $sPropertyNameKey => $aPropertyDetails) {
					// parse each property
					$o->amProperties[$sPropertyNameKey] = [];
					foreach ($aPropertyDetails as $sPopertyPropertyName => $sPopertyPropertyValue)
					{
						$o->amProperties[$sPropertyNameKey][$sPopertyPropertyName] = $sPopertyPropertyValue;
					}
				}
			}

			return $o;
		}

		public function save()
		{
			// persist model to disk, returns model id or null

			$sFileContents = json_encode(get_object_vars($this));

			if(FileController::bSaveModel($this->sUId, $sFileContents))
				return $this->sUId;

			return null;
		}

		public function _SetProperty($sKey, $mValue)
		{
			if(isset($this->amProperties[$sKey]))
				$this->amProperties[$sKey]['value'] = $mValue;
		}
		public function mGetProperty($sKey)
		{
			return (isset($this->amProperties[$sKey]['value'])) ? $this->amProperties[$sKey]['value'] : null;
		}

		public function aGetPropertiesForCollection()
		{
			$aReturn = [];

			foreach ($this->amProperties as $sPropertyName => $aPropertyProperties) {
				if($aPropertyProperties['exposed_to_collection'] == true)
					$aReturn[$sPropertyName] = $aPropertyProperties['value'];
			}

			return $aReturn;
		}
	}