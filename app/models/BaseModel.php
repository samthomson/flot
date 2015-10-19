<?php

	class BaseModel
	{
		protected $amProperties = [];
		protected $sType = "";
		protected $sCollection = "";
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
			$sFilePath = $GLOBALS['files.models_path']."item_$sUId.flotcms";

			$oParsed = self::createFromJson(FileController::sReadTextFromFile($sFilePath));

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
					// parse each prroperty
					$o->amProperties[$sPropertyNameKey] = [];
					foreach ($aPropertyDetails as $sPopertyPropertyName => $sPopertyPropertyValue)
					{
						$o->amProperties[$sPropertyNameKey][$sPopertyPropertyName] = $sPopertyPropertyValue;

						if($sPropertyNameKey === 'published')
						{
							echo "$sPropertyNameKey - $sPopertyPropertyName: ", $sPopertyPropertyValue, "<br/>";


						}

						/*
						if((string)$sPopertyPropertyName === "type" && (string)$sPopertyPropertyValue === "boolean")
						{
							$o->amProperties[$sPropertyNameKey]['value'] = filter_var($sPopertyPropertyValue, FILTER_VALIDATE_BOOLEAN);
							echo "force to bool";
						}*/
			
					}
				}
			}

			return $o;
		}

		public function save()
		{
			// persist model to disk, returns model id or null
			$iReturn = null;
			// save myself
			$sFileContents = json_encode(get_object_vars($this));

			////echo "save: ", $sFileContents, "<br/>";

			////echo "saving: $sFileContents";exit();

			if(FileController::bSaveModel($this->sUId, $sFileContents))
				$iReturn = $this->sUId;

			// save my collection
			//$this->sCollection::updateItem($this);
			call_user_func([$this->sCollection, 'updateItem'], $this);

			return $iReturn;
		}

		public function _SetProperty($sKey, $mValue)
		{
			echo "about to set property ($sKey)", "<br/>";
			echo "was of type: ", gettype($this->amProperties[$sKey]['value']), "<br/>";



			if(isset($this->amProperties[$sKey]) && isset($this->amProperties[$sKey]['type']))
			{
				// ensure we set the type properly
				switch ($this->amProperties[$sKey]['type']) {
					case 'boolean':
						$this->amProperties[$sKey]['value'] = filter_var($mValue, FILTER_VALIDATE_BOOLEAN);
						break;
					
					default: # string
						$this->amProperties[$sKey]['value'] = $mValue;
						break;
				}
				
			}


			echo "now of type: ", gettype($this->amProperties[$sKey]['value']), "<br/><br/>";
		}
		public function mGetProperty($sKey, $bString = false)
		{
			$mReturn = null;

			$mReturn = (isset($this->amProperties[$sKey]['value'])) ? $this->amProperties[$sKey]['value'] : null;

			return ($bString ? var_export($mReturn, true) : $mReturn);
		}
		public function aGetAllProperties()
		{
			return $this->amProperties;
		}
		public function aGetKeyValueProperties()		
		{
			$maParams = [];

			foreach($this->amProperties as $sKey => $aValue)
			{
				$maParams[$sKey] = $aValue['value'];
			}

			return $maParams;
		}

		public function aGetPropertiesForCollection()
		{
			$aReturn = [];

			// for each property
			foreach ($this->amProperties as $sPropertyName => $aPropertyProperties) {
				if($aPropertyProperties['exposed_to_collection'] == true)

					# now we just return the whole item
					#$aReturn[$sPropertyName] = $aPropertyProperties['value'];

					$aReturn[$sPropertyName] = $aPropertyProperties;
			}

			return $aReturn;
		}
	}