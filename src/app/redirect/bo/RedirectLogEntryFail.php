<?php

namespace redirect\bo;

use n2n\persistence\orm\annotation\AnnoDiscriminatorValue;
use n2n\reflection\annotation\AnnoInit;

class RedirectLogEntryFail extends RedirectLogEntryAdapter {
	private static function _annos(AnnoInit $ai) {
		$ai->c(new AnnoDiscriminatorValue('FAIL'));
	}

	public function __construct($fromUrl = null, $description = null, $success = null) {
		parent::__construct($fromUrl, $description, $success);
	}
}