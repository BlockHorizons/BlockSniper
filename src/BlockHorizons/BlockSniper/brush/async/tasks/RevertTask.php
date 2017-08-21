<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\brush\async\tasks;

use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use BlockHorizons\BlockSniper\undo\async\AsyncRevert;
use BlockHorizons\BlockSniper\undo\Revert;
use pocketmine\level\format\Chunk;
use pocketmine\level\Level;
use pocketmine\Server;

class RevertTask extends AsyncBlockSniperTask {

	/** @var int */
	protected $taskType = self::TYPE_REVERT;
	/** @var string */
	private $revert = "";

	public function __construct(AsyncRevert $revert, Server $server) {
		$this->revert = serialize($revert);
	}

	public function onRun() {
		/** @var AsyncRevert $revert */
		$revert = unserialize($this->revert);
		$chunks = $revert->getModifiedChunks();
		$revert->setManager($manager = BaseType::establishChunkManager($chunks));

		$detached = $revert->getDetached();
		$revert->restore();

		$this->setResult([
			"chunks" => $chunks,
			"revert" => $detached
		]);
	}

	/**
	 * @param Server $server
	 *
	 * @return bool
	 */
	public function onCompletion(Server $server): bool {
		/** @var Loader $loader */
		$loader = $server->getPluginManager()->getPlugin("BlockSniper");
		if($loader === null) {
			return false;
		}
		if(!$loader->isEnabled()) {
			return false;
		}
		$result = $this->getResult();
		/** @var Revert $revert */
		$revert = $result["revert"];
		if(!($player = $server->getPlayer($revert->getPlayerName()))) {
			return false;
		}
		/** @var Chunk[] $chunks */
		$chunks = $result["chunks"];
		$levelId = $player->getLevel()->getId();
		$level = $server->getLevel($levelId);
		if($level instanceof Level) {
			foreach($chunks as $hash => $chunk) {
				$x = $z = 0;
				Level::getXZ($hash, $x, $z);
				$level->setChunk($x, $z, $chunk);
			}
		}
		SessionManager::getPlayerSession($player)->getRevertStorer()->saveRevert($revert);
		return true;
	}


	/**
	 * @param Server $server
	 * @param mixed  $progress
	 *
	 * @return bool
	 */
	public function onProgressUpdate(Server $server, $progress): bool {
		$loader = $server->getPluginManager()->getPlugin("BlockSniper");
		if($loader instanceof Loader) {
			if($loader->isEnabled()) {
				$loader->getLogger()->debug($progress);
				return true;
			}
		}
		$this->setGarbage();
		return false;
	}
}