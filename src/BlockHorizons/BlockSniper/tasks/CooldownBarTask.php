<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\tasks;

use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use function ceil;
use function microtime;
use function str_repeat;

class CooldownBarTask extends BlockSniperTask{

	/** @var Player */
	private $player;
	/** @var int */
	private $useTime;

	public function __construct(Loader $loader, Player $player){
		parent::__construct($loader);
		$this->player = $player;
		// Time of usage in seconds.
		$this->useTime = microtime(true);
	}

	public function onRun(int $currentTick) : void{
		if($this->player->isClosed()){
			$this->getHandler()->cancel();
			return;
		}
		do {
			if($this->loader->config->cooldownSeconds === 0.0){
				break;
			}
			$progress = (int) ceil((microtime(true) - $this->useTime) / $this->loader->config->cooldownSeconds * 20);
			if($progress > 20){
				$progress = 20;
			}
			$this->player->sendPopup(TextFormat::AQUA . str_repeat("|", $progress) . TextFormat::GRAY . str_repeat("|", 20 - $progress));

			if($progress === 20){
				break;
			}
			return;
		}while(false);

		SessionManager::getPlayerSession($this->player)->getBrush()->unlock();
		$this->player->sendPopup(TextFormat::AQUA . Translation::get(Translation::BRUSH_STATE_READY));
		$this->getHandler()->cancel();
	}
}