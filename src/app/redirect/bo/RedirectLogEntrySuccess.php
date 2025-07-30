<?php
namespace redirect\bo;

use n2n\persistence\orm\annotation\AnnoDiscriminatorValue;
use n2n\reflection\annotation\AnnoInit;
use rocket\attribute\EiMenuItem;
use rocket\attribute\EiType;
use rocket\attribute\EiPreset;
use rocket\op\spec\setup\EiPresetMode;

#[EiMenuItem(groupName: 'Redirect')]
#[EiType()]
#[EiPreset(EiPresetMode::READ)]
class RedirectLogEntrySuccess extends RedirectLogEntryAdapter {
	private static function _annos(AnnoInit $ai) {
		$ai->c(new AnnoDiscriminatorValue('SUCCESS'));
	}

	public function __construct($fromUrl = null, $description = null) {
		parent::__construct($fromUrl, $description);
	}
}