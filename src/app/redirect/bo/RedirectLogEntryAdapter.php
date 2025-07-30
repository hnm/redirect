<?php
namespace redirect\bo;

use n2n\persistence\orm\annotation\AnnoDiscriminatorColumn;
use n2n\persistence\orm\annotation\AnnoInheritance;
use n2n\persistence\orm\annotation\AnnoTable;
use n2n\persistence\orm\InheritanceType;
use n2n\reflection\annotation\AnnoInit;
use n2n\reflection\ObjectAdapter;
use n2n\util\uri\Url;
use rocket\attribute\EiType;
use rocket\attribute\EiPreset;
use rocket\op\spec\setup\EiPresetMode;
use rocket\attribute\impl\EiCmdDelete;

#[EiType()]
#[EiPreset(EiPresetMode::READ)]
#[EiCmdDelete]
abstract class RedirectLogEntryAdapter extends ObjectAdapter implements RedirectLogEntry {
	private static function _annos(AnnoInit $ai) {
		$ai->c(new AnnoTable('redirect_log_entry'), new AnnoInheritance(InheritanceType::SINGLE_TABLE),
				new AnnoDiscriminatorColumn('type'));
	}

	protected $id;
	protected $fromUrlStr;
	protected $description;
	protected $created;

	/**
	 * RedirectLogEntry constructor.
	 * @param Url|null $fromUrl
	 * @param string|null $description
	 */
	public function __construct(?Url $fromUrl = null, ?string $description = null) {
		$this->fromUrlStr = (string) $fromUrl;
		$this->description = $description;
		$this->created = new \DateTime();
	}

	/**
	 * An easier way to create RedirectLogEntries with message
	 * @param Url $url
	 * @param RedirectRule $redirectRule
	 * @param \Error|null $e
	 * @return RedirectLogEntry
	 */
	public static function buildWithMessage(Url $url, RedirectRule $redirectRule, bool $success = true, ?string $errorMessage = null) {
		if ($success) {
			return new RedirectLogEntrySuccess($url, self::createMessage($url, $redirectRule, $errorMessage));
		}
		return new RedirectLogEntryFail($url, self::createMessage($url, $redirectRule, $errorMessage));
	}

	/**
	 * @param Url $url
	 * @param RedirectRule $redirectRule
	 * @param \Error|null $e
	 * @return string
	 */
	public static function createMessage(Url $url, RedirectRule $redirectRule, ?string $errorMessage = null) {
		$message = '';
		if ($errorMessage !== null) {
			$message .= 'ERROR';
			$message .= PHP_EOL . 'Message: ' . $errorMessage;
		} else {
			$message .= 'SUCCESS';
		}

		$message .= PHP_EOL . 'Url: ' . $url;
		$message .= PHP_EOL . 'TargetUrl: ' . $redirectRule->getTargetUrlStr();
		$message .= PHP_EOL . 'HostPattern: ' . $redirectRule->getHostPattern();
		$message .= PHP_EOL . 'PathPattern: ' . $redirectRule->getPathPattern();

		return $message;
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
	public function setId($id) {
		$this->id = $id;
	}

	/**
	 * @return string
	 */
	public function getFromUrlStr() {
		return $this->fromUrlStr;
	}

	public function getFromUrl() {
		return Url::build($this->fromUrlStr);
	}

	/**
	 * @param string $fromUrlStr
	 */
	public function setFromUrlStr(string $fromUrlStr) {
		$this->fromUrlStr = $fromUrlStr;
	}

	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * @param string $description
	 */
	public function setDescription(string $description) {
		$this->description = $description;
	}

	/**
	 * @return \DateTime
	 */
	public function getCreated(): \DateTime {
		return $this->created;
	}

	/**
	 * @param \DateTime $created
	 */
	public function setCreated(\DateTime $created) {
		$this->created = $created;
	}

	/**
	 * @return mixed
	 */
	public function getRedirectNotFoundLogEntry() {
		return $this->redirectNotFoundLogEntry;
	}

	/**
	 * @param mixed $redirectNotFoundLogEntry
	 */
	public function setRedirectNotFoundLogEntry($redirectNotFoundLogEntry) {
		$this->redirectNotFoundLogEntry = $redirectNotFoundLogEntry;
	}
}