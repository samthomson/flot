<?php

	class PageCollectionModel extends CollectionModel
	{

		private $amItems = [];

		public function __construct() {
        	parent::__construct();

        	$this->amPropertiesToExposeOfItems = [
				'title'
			];

			$this->sName = "pages";
		}


		public static function create()
		{
			return new static();
		}

	}