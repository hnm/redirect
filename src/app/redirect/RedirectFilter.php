<?php
namespace redirect;

use n2n\core\container\TransactionManager;
use n2n\web\http\controller\ControllerAdapter;
use n2n\web\http\Request;
use n2n\web\http\Response;
use n2n\web\http\ResponseListener;
use redirect\model\RedirectDao;
use redirect\model\Redirector;
use n2n\web\http\payload\Payload;

class RedirectFilter extends ControllerAdapter {
	public function index(RedirectDao $redirectDao, Request $request, TransactionManager $tm) {
		$redirector = new Redirector($request->getUrl());
		$rl = new RedirectListener($redirector, $redirectDao, $tm);
		$this->getResponse()->registerListener($rl);
	}
}

class RedirectListener implements ResponseListener {
	/**
	 * @var Redirector
	 */
	private $redirector;
	/**
	 * @var RedirectDao
	 */
	private $redirectDao;

	private TransactionManager $tm;

	public function __construct(Redirector $redirector, RedirectDao $redirectDao, TransactionManager $tm) {
		$this->redirector = $redirector;
		$this->redirectDao = $redirectDao;
		$this->tm = $tm;
	}

	public function onFlush(Response $response) {
		if ($response->getStatus() !== Response::STATUS_404_NOT_FOUND) return;

		$this->redirectDao->buildNotFoundLogEntry($response->getRequest()->getUrl());
		$this->redirector->redirect($this->redirectDao->getRedirectRules(), $response);

		$logTransaction = $this->tm->createTransaction();
		$this->redirectDao->log($this->redirector->getRedirectLogEntries());
		$logTransaction->commit();
	}

	public function onStatusChange(int $newStatus, Response $response) {}

	public function onSend(Payload $payload, Response $response) {}

	public function onReset(Response $response) {}
}