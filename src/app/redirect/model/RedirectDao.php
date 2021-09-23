<?php
namespace redirect\model;

use n2n\context\RequestScoped;
use n2n\core\container\TransactionManager;
use n2n\persistence\orm\EntityManager;
use n2n\util\uri\Url;
use redirect\bo\RedirectLogEntry;
use redirect\bo\RedirectLogEntryAdapter;
use redirect\bo\RedirectLogEntryNotFound;
use redirect\bo\RedirectRule;

class RedirectDao implements RequestScoped {
	/**
	 * @var EntityManager
	 */
	private $em;

	/**
	 * @var TransactionManager
	 */
	private $tm;

	private function _init(EntityManager $em, TransactionManager $tm) {
		$this->em = $em;
		$this->tm = $tm;
	}

	/**
	 * @return RedirectRule[]
	 */
	public function getRedirectRules() {
		return $this->em->createSimpleCriteria(RedirectRule::getClass(), null, array('orderIndex' => 'ASC'))
				->toQuery()->fetchArray();
	}

	/**
	 * @param RedirectLogEntry[] $redirectLogEntries
	 */
	public function log(array $redirectLogEntries) {
		foreach ($redirectLogEntries as $redirectLogEntry) {
			$this->em->persist($redirectLogEntry);
		}
	}

	/**
	 * @param Url $url
	 */
	public function buildNotFoundLogEntry(Url $url) {
		$notFoundLogEntry = $this->em->createSimpleCriteria(RedirectLogEntryNotFound::getClass(),
				array('urlStr' => (string) $url))->limit(1)->toQuery()->fetchSingle();
		$tx = $this->tm->createTransaction();
		if ($notFoundLogEntry === null) {
			$notFoundLogEntry = new RedirectLogEntryNotFound($url);
			$this->em->persist($notFoundLogEntry);
		}

		$notFoundLogEntry->setLastOccurence(new \DateTime());
		$notFoundLogEntry->setCount($notFoundLogEntry->getCount() + 1);
		$tx->commit();
	}

	/**
	 * @param Url $url
	 */
	public function removeRedirectLogsByUrl(Url $url) {
		$redirectLogEntries = $this->em->createSimpleCriteria(RedirectLogEntryAdapter::getClass(), array('fromUrlStr' => (string) $url))
				->toQuery()->fetchArray();

		foreach ($redirectLogEntries as $redirectLogEntry) {
			$this->em->remove($redirectLogEntry);
		}
	}
}