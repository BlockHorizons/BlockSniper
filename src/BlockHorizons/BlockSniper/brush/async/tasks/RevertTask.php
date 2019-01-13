<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\async\tasks;

use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\revert\async\AsyncRevert;
use BlockHorizons\BlockSniper\revert\Revert;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use pocketmine\level\format\Chunk;
use pocketmine\level\Level;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

class RevertTask extends AsyncTask{

	/** @var AsyncRevert */
	private $revert;

	public function __construct(AsyncRevert $revert){
		$this->revert = $revert;
	}

	public function onRun() : void{
		$revert = $this->revert;
		$chunks = $revert->getOldChunks();

		$revert = $revert->getDetached();

		$this->setResult([$chunks, $revert]);
	}

	public function onCompletion() : void{
		/** @var Loader $loader */
		$loader = Server::getInstance()->getPluginManager()->getPlugin("BlockSniper");
		if(!$loader->isEnabled()){
			return;
		}

		$result = $this->getResult();
		/** @var Chunk[] $chunks */
		$chunks = $result[0];
		/** @var Revert $revert */
		$revert = $result[1];
		if(!($player = Server::getInstance()->getPlayer($revert->getPlayerName()))){
			return;
		}

		$levelId = $player->getLevel()->getId();
		$level = Server::getInstance()->getLevelManager()->getLevel($levelId);

		if($level instanceof Level){
			foreach($chunks as $hash => $chunk){
				$level->setChunk($chunk->getX(), $chunk->getZ(), $chunk, false);
			}
		}

		SessionManager::getPlayerSession($player)->getRevertStore()->saveRevert($revert);
	}
}
