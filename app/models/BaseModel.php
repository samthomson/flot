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
						//array_push($o->amProperties[$sPropertyNameKey] , [$sPopertyPropertyName => $sPopertyPropertyValue]);
						$o->amProperties[$sPropertyNameKey][$sPopertyPropertyName] = $sPopertyPropertyValue;
					}
				}
				//print_r($o->amProperties);
				//$o->title = $oJson->title;
				//echo $o->title, "<br/>";
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
	}