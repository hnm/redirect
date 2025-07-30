<?php
namespace redirect\bo;

use n2n\persistence\orm\annotation\AnnoTransient;
use n2n\reflection\annotation\AnnoInit;
use n2n\reflection\ObjectAdapter;
use n2n\util\uri\Url;
use n2n\core\err\WarningError;
use rocket\attribute\EiMenuItem;
use rocket\attribute\EiType;
use rocket\attribute\EiPreset;
use rocket\op\spec\setup\EiPresetMode;
use rocket\attribute\impl\EiPropOrder;
use rocket\attribute\impl\EiPropBool;
use rocket\attribute\impl\EiPropString;

#[EiMenuItem(groupName: 'Redirect')]
#[EiType()]
#[EiPreset(EiPresetMode::EDIT, excludeProps: ['logEntries'])]
class RedirectRule extends ObjectAdapter {
	private static function _annos(AnnoInit $ai) {
		$ai->p('logEntries', new AnnoTransient());
	}

	const REGEX_DELIMITER = "~";

	private $id;
	private $hostPattern;
	private $pathPattern;
	private $targetUrlStr;
	#[EiPropOrder]
	private $orderIndex;
	#[EiPropBool]
	private $isRegex;

	private $logEntries = array();

	public function matches(Url $url) {
		if (!$this->getIsRegex()) {
			try {
				return $this->urlEqual($url);
			} catch (\InvalidArgumentException $e) {
				$this->logEntries[] = RedirectLogEntryAdapter::buildWithMessage($url, $this, false, $e->getMessage());
				return false;
			}
		}

		if (!$this->checkRedirectRulePattern($url, $this->getHostPattern(), $url->getAuthority()->getHost(), false)) {
			return false;
		}
		if (!$this->checkRedirectRulePattern($url, $this->getPathPattern(), $url->getPath())) {
			return false;
		}

		return true;
	}

	private function urlEqual(Url $url) {
		if ($this->hostPattern !== null && (string) $url->getAuthority() !== $this->hostPattern) {
			return false;
		}

		if ($this->pathPattern !== null && (string) $url->getPath() !== $this->pathPattern) {
			return false;
		}

		return true;
	}

	/**
	 * @return RedirectLogEntry[]
	 */
	public function getLogEntries() {
		return $this->logEntries;
	}

	/**
	 * @param Url $url
	 * @param string $pattern
	 * @param bool $required
	 * @return bool
	 */
	private function checkRedirectRulePattern(Url $url, ?string $pattern, string $compareStr, bool $required = true) {
		if (!$required && $pattern === null) return true;

		if (strpos($pattern, self::REGEX_DELIMITER) > -1) {
			$this->logEntries[] = RedirectLogEntryAdapter::buildWithMessage($url, $this,
				false, self::REGEX_DELIMITER . ' is not allowed character for redirect module regex.');
			return false;
		}

		try {
			$pattern = self::REGEX_DELIMITER . $pattern  . self::REGEX_DELIMITER;
			if (!preg_match($pattern, $compareStr)) return false;
		} catch (WarningError $e) {
			$this->logEntries[] = RedirectLogEntryAdapter::buildWithMessage($url, $this, false, $e->getMessage());
			return false;
		}

		return true;
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param int $id
	 */
	public function setId(int $id) {
		$this->id = $id;
	}

	/**
	 * @return string
	 */
	public function getHostPattern() {
		return $this->hostPattern;
	}

	/**
	 * @param string $hostPattern
	 */
	public function setHostPattern(?string $hostPattern = null) {
		$this->hostPattern = $hostPattern !== null ? trim($hostPattern) : null;
	}

	/**
	 * @return string
	 */
	public function getPathPattern() {
		return $this->pathPattern;
	}

	/**
	 * @param string $pathPattern
	 */
	public function setPathPattern(string $pathPattern) {
		$this->pathPattern = trim($pathPattern);
	}

	public function getPatternUrl() {

	}
	/**
	 * @return Url
	 */
	public function getTargetUrl() {
		return Url::create($this->targetUrlStr);
	}

	public function getTargetUrlStr() {
		return $this->targetUrlStr;
	}

	/**
	 * @param Url $targetUrlStr
	 */
	public function setTargetUrlStr(string $targetUrlStr) {
		$this->targetUrlStr = Url::create($targetUrlStr, true)->__toString();
	}

	/**
	 * @return mixed
	 */
	public function getOrderIndex() {
		return $this->orderIndex;
	}

	/**
	 * @param mixed $orderIndex
	 */
	public function setOrderIndex($orderIndex) {
		$this->orderIndex = $orderIndex;
	}

	/**
	 * @return mixed
	 */
	public function getIsRegex() {
		return (bool) $this->isRegex;
	}

	/**
	 * @param mixed $isRegex
	 */
	public function setIsRegex($isRegex) {
		$this->isRegex = $isRegex;
	}
}