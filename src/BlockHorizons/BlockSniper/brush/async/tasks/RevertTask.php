<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\brush\async\tasks;

use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\undo\Redo;
use BlockHorizons\BlockSniper\undo\Revert;
use BlockHorizons\BlockSniper\undo\Undo;
use pocketmine\level\format\Chunk;
use pocketmine\level\Level;
use pocketmine\Server;

class RevertTask extends AsyncBlockSniperTask {

	/** @var int */
	protected $type = self::TYPE_REVERT;
	/** @var string */
	private $revert = "";

	public function __construct(Revert $revert) {
		$this->revert = serialize($revert);
	}

	public function onRun() {
		/** @var Undo|Redo $revert */
		$revert = unserialize($this->revert);
		$chunks = $revert->getTouchedChunks();
		$revert->setManager($manager = BaseType::establishChunkManager($chunks));

		$detached = $revert->getDetached();
		$revert->restore($this);
		$this->setResult([
			"chunks" => $chunks,
			"revert" => $detached
		]);
	}

	public function onCompletion(Server $server) {
		/** @var Loader $loader */
		$loader = $server->getPluginManager()->getPlugin("BlockSniper");
		if($loader === null) {
			return false;
		}
		if(!$loader->isEnabled()) {
			return false;
		}
		$levelId = 0;
		$result = $this->getResult();
		/** @var Revert $revert */
		$revert = $result["revert"];
		/** @var Chunk[] $chunks */
		$chunks = $result["chunks"];
		foreach($revert->getBlocks() as $block) {
			$levelId = $block->getId();
			break;
		}
		$level = $server->getLevel($levelId);
		if($level instanceof Level) {
			foreach($chunks as $hash => $chunk) {
				$x = $z = 0;
				Level::getXZ($hash, $x, $z);
				$level->setChunk($x, $z, $chunk);
			}
		}
		if(($player = $server->getPlayer($revert->getPlayerName()))) {
			$loader->getRevertStorer()->saveRevert($revert, $player);
		}
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
		$loader->getLogger()->debug($progress);
		if($loader instanceof Loader) {
			if($loader->isEnabled()) {
				return true;
			}
		}
		$this->setGarbage();
		return false;
	}
}