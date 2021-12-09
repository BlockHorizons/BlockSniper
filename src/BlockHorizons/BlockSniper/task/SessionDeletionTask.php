<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\task;

use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\session\PlayerSession;
use BlockHorizons\BlockSniper\session\SessionManager;

class SessionDeletionTask extends BlockSniperTask{

	/** @var PlayerSession */
	private $session;

	public function __construct(Loader $loader, PlayerSession $session){
		parent::__construct($loader);
		$this->session = $session;
	}

	public function onRun() : void{
		if(($player = $this->session->getSessionOwner()->getPlayer()) === null){
			SessionManager::closeSession($this->session->getSessionOwner()->getName());
		}
	}
}