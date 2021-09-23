<?php
namespace redirect\model;

use n2n\core\err\WarningError;
use n2n\util\uri\Url;
use n2n\web\http\Redirect;
use n2n\web\http\Response;
use redirect\bo\RedirectLogEntry;
use redirect\bo\RedirectLogEntryAdapter;
use redirect\bo\RedirectRule;
use redirect\exception\RedirectRuleInvalidTargetUrl;

class Redirector {
	private $url;
	private $host;
	private $path;
	private $redirectLogEntries = array();
	private $lastCheckedRedirectRule;

	/**
	 * Redirector constructor.
	 * @param $hostUrl
	 * @param $path
	 */
	public function __construct(Url $url) {
		$authority = $url->getAuthority()->getHost();
		$this->host = substr((string) $url, 0, strpos((string) $url, $authority) + strlen($authority));
		$this->path = $url->getPath();
		$this->url = $url;
	}

	/**
	 * @param RedirectRule[] $redirectRules
	 * @param bool $checkRedirectRules
	 * @return bool|mixed|null
	 */
	public function findRedirectRule(array $redirectRules) {
		if (null === ($redirectRule = array_shift($redirectRules))) {
			return null;
		}

		$isMatch = $redirectRule->matches($this->url);
		$this->redirectLogEntries = array_merge($this->redirectLogEntries, $redirectRule->getLogEntries());

		if ($isMatch) {
			return $redirectRule;
		}

		return $this->findRedirectRule($redirectRules);
	}

	public function redirect(array $redirectRules, Response $response) {
		try {
			if (!$this->checkRedirectRules($redirectRules)) {
				return false;
			}
		} catch (RedirectRuleInvalidTargetUrl $e) {
			$this->redirectLogEntries[] = RedirectLogEntryAdapter::buildWithMessage($this->url, $this->lastCheckedRedirectRule, false, $e->getMessage());
			return null;
		}

		$redirectRule = $this->findRedirectRule($redirectRules);

		if (null === $redirectRule) return false;

		$this->redirectLogEntries[] = RedirectLogEntryAdapter::buildWithMessage($this->url, $redirectRule);
		$response->reset();
		$response->send(new Redirect($redirectRule->getTargetUrl()));
		return true;
	}

	/**
	 * @param RedirectRule[] $redirectRules
	 * @return bool
	 * @throws RedirectRuleInvalidTargetUrl
	 */
	private function checkRedirectRules(array $redirectRules) {
		foreach ($redirectRules as $redirectRule) {
			$targetUrl = $redirectRule->getTargetUrl();

			if ($targetUrl->isRelative()) {
				$targetUrl = Url::create($targetUrl);
				if ((string) $targetUrl === (string) $this->url->toRelativeUrl()) {
					$this->lastCheckedRedirectRule = $redirectRule;
					throw new RedirectRuleInvalidTargetUrl('InvalidTargetURL: Target Url points to Not Found');
				}
			}
			if ((string) $targetUrl === (string) $this->url) {
				$this->lastCheckedRedirectRule = $redirectRule;
				throw new RedirectRuleInvalidTargetUrl('InvalidTargetURL: Target Url points to Not Found');
			}
		}

		return true;
	}

	public function getRedirectLogEntries() {
		return $this->redirectLogEntries;
	}
}