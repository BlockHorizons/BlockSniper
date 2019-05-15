<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\cloning;

use BlockHorizons\BlockSniper\brush\async\tasks\PasteTask;
use BlockHorizons\BlockSniper\brush\Shape;
use BlockHorizons\BlockSniper\revert\SyncRevert;
use BlockHorizons\BlockSniper\sessions\Session;
use pocketmine\block\Air;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\world\Position;

class CloneStore{

	/** @var Shape */
	private $copy = null;
	/** @var Vector3 */
	private $offsetPosition = null;
	/** @var string */
	private $path = "";
	/** @var Session */
	private $session;

	public function __construct(Session $session, string $path){
		$this->session = $session;
		$this->path = $path;
	}

	/**
	 * @param Shape   $shape
	 * @param Vector3 $offsetPosition
	 */
	public function saveCopy(Shape $shape, Vector3 $offsetPosition) : void{
		$this->copy = $shape;
		$this->offsetPosition = $offsetPosition;
	}

	/**
	 * @param Position $targetBlock
	 */
	public function pasteCopy(Position $targetBlock) : void{
		$undoBlocks = [];

		foreach($this->copy->getBlocks($targetBlock->getWorld()) as $block){
			if($block instanceof Air){
				continue;
			}
			$v3 = $block->subtract($this->offsetPosition);

			$undoBlocks[] = $targetBlock->world->getBlock($targetBlock->add($v3));
			$targetBlock->world->setBlock($targetBlock->add($v3), clone $block, false);
		}
		$this->session->getRevertStore()->saveUndo(new SyncRevert($undoBlocks, $targetBlock->getWorld()));
	}

	/**
	 * @return bool
	 */
	public function copyStoreExists() : bool{
		return $this->copy !== null;
	}

	/**
	 * @param string  $file
	 * @param Vector3 $center
	 * @param array   $chunks
	 */
	public function pasteSchematic(string $file, Vector3 $center, array $chunks) : void{
		Server::getInstance()->getAsyncPool()->submitTask(new PasteTask($file, $center, $chunks, $this->session->getSessionOwner()->getName()));
	}
}