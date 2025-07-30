<?php

namespace redirect\bo;

use n2n\util\uri\Url;

interface RedirectLogEntry {
	public static function buildWithMessage(Url $url, RedirectRule $redirectRule, bool $success = true, ?string $errorMessage = null);
	public static function createMessage(Url $url, RedirectRule $redirectRule, ?string $errorMessage = null);
}