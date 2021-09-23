<?php
namespace redirect\bo;

use n2n\reflection\ObjectAdapter;
use n2n\util\uri\Url;
use redirect\model\RedirectDao;

class RedirectLogEntryNotFound extends ObjectAdapter {
	private $id;
	private $count;
	private $urlStr;
	private $lastOccurence;

	public function __construct(Url $url = null, $count = 0) {
		$this->urlStr = (string) $url;
		$this->count = $count;
		$this->lastOccurence = new \DateTime('now');
	}

	/**
	 * @return mixed
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param mixed $id
	 */
	public function setId($id) {
		$this->id = $id;
	}

	/**
	 * @return mixed
	 */
	public function getCount() {
		return $this->count;
	}

	/**
	 * @param mixed $count
	 */
	public function setCount($count) {
		$this->count = $count;
	}

	/**
	 * @return mixed
	 */
	public function getUrlStr() {
		return $this->urlStr;
	}

	/**
	 * @param mixed $urlStr
	 */
	public function setUrlStr($urlStr) {
		$this->urlStr = $urlStr;
	}

	/**
	 * @return Url|null
	 */
	public function getUrl() {
		return Url::build($this->urlStr);
	}

	/**
	 * @return \DateTime
	 */
	public function getLastOccurence() {
		return $this->lastOccurence;
	}

	/**
	 * @param \DateTime $lastOccurence
	 */
	public function setLastOccurence(\DateTime $lastOccurence) {
		$this->lastOccurence = $lastOccurence;
	}

	private function _postRemove(RedirectDao $redirectDao) {
		$redirectDao->removeRedirectLogsByUrl(Url::create($this->urlStr));
	}
}