<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\tasks;

use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\sessions\PlayerSession;
use BlockHorizons\BlockSniper\sessions\SessionManager;

class SessionDeletionTask extends BaseTask{

	/** @var PlayerSession */
	private $session;

	public function __construct(Loader $loader, PlayerSession $session){
		parent::__construct($loader);
		$this->session = $session;
	}

	public function onRun(int $currentTick) : void{
		if(($player = $this->session->getSessionOwner()->getPlayer()) === null){
			SessionManager::closeSession($this->session->getSessionOwner()->getName());
		}
	}
}