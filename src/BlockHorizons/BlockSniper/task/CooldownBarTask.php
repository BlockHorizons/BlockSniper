<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\task;

use BlockHorizons\BlockSniper\brush\Brush;
use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use pocketmine\player\Player;
use pocketmine\scheduler\CancelTaskException;
use pocketmine\utils\TextFormat;
use function ceil;
use function microtime;
use function str_repeat;

class CooldownBarTask extends BlockSniperTask{

	/** @var Player */
	private $player;
	/** @var Brush */
	private $brush;
	/** @var float */
	private $useTime;

	public function __construct(Loader $loader, Brush $brush, Player $player){
		parent::__construct($loader);
		$this->player = $player;
		$this->brush = $brush;
		// Time of usage in seconds.
		$this->useTime = microtime(true);
	}

	public function onRun() : void{
		if($this->player->isClosed()){
			throw new CancelTaskException();
		}
		do{
			if($this->loader->config->cooldownSeconds === 0.0){
				break;
			}
			$progress = (int) ceil((microtime(true) - $this->useTime) / $this->loader->config->cooldownSeconds * 20);
			if($progress > 20){
				$progress = 20;
			}
			$this->player->sendPopup(TextFormat::AQUA . str_repeat("|", $progress) . TextFormat::GRAY . str_repeat("|", 20 - $progress));

			if($progress === 20){
				$this->player->sendPopup(TextFormat::AQUA . Translation::get(Translation::BRUSH_STATE_READY));
				break;
			}

			return;
		}while(false);

		$this->brush->unlock();
		throw new CancelTaskException();
	}
}