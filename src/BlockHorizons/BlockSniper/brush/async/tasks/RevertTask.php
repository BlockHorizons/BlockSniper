<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\async\tasks;

use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\revert\async\AsyncRevert;
use BlockHorizons\BlockSniper\revert\Revert;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use pocketmine\level\format\Chunk;
use pocketmine\level\Level;
use pocketmine\Server;

class RevertTask extends AsyncBlockSniperTask{

	/** @var string */
	private $revert = "";

	public function __construct(AsyncRevert $revert){
		$this->revert = serialize($revert);
	}

	public function onRun() : void{
		/** @var AsyncRevert $revert */
		$revert = unserialize($this->revert, ["allowed_classes" => true]);
		$chunks = $revert->getOldChunks();

		$revert = $revert->getDetached();

		$this->setResult(compact("chunks", "revert"));
	}

	/**
	 * @param Server $server
	 *
	 * @return bool
	 */
	public function onCompletion(Server $server) : bool{
		/** @var Loader $loader */
		$loader = $server->getPluginManager()->getPlugin("BlockSniper");
		if($loader === null){
			return false;
		}
		if(!$loader->isEnabled()){
			return false;
		}

		$result = $this->getResult();
		/** @var Revert $revert */
		$revert = $result["revert"];
		if(!($player = $server->getPlayer($revert->getPlayerName()))){
			return false;
		}

		/** @var Chunk[] $chunks */
		$chunks = $result["chunks"];
		$levelId = $player->getLevel()->getId();
		$level = $server->getLevel($levelId);

		if($level instanceof Level){
			foreach($chunks as $hash => $chunk){
				$level->setChunk($chunk->getX(), $chunk->getZ(), $chunk, false);
			}
		}

		SessionManager::getPlayerSession($player)->getRevertStore()->saveRevert($revert);

		return true;
	}
}
