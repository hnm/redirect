<?php
namespace redirect\bo;

use n2n\persistence\orm\annotation\AnnoDiscriminatorValue;
use n2n\reflection\annotation\AnnoInit;

class RedirectLogEntrySuccess extends RedirectLogEntryAdapter {
	private static function _annos(AnnoInit $ai) {
		$ai->c(new AnnoDiscriminatorValue('SUCCESS'));
	}

	public function __construct($fromUrl = null, $description = null) {
		parent::__construct($fromUrl, $description);
	}
}