<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\cloning;

use BlockHorizons\BlockSniper\brush\async\tasks\PasteTask;
use BlockHorizons\BlockSniper\brush\Shape;
use BlockHorizons\BlockSniper\revert\sync\SyncUndo;
use BlockHorizons\BlockSniper\sessions\Session;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Server;

class CloneStore{

	/** @var Shape */
	private $copy = null;
	/** @var Vector3 */
	private $originalCenter = null;
	/** @var string */
	private $path = "";
	/** @var Session */
	private $session;

	public function __construct(Session $session, string $path){
		$this->session = $session;
		$this->path = $path;
	}

	/**
	 * @param Shape   $generator
	 * @param Vector3 $center
	 */
	public function saveCopy(Shape $generator, Vector3 $center) : void{
		$this->copy = $generator;
		$this->originalCenter = $center;
	}

	/**
	 * @return Vector3
	 */
	public function getOriginalCenter() : Vector3{
		return $this->originalCenter;
	}

	/**
	 * @param Vector3 $center
	 */
	public function setOriginalCenter(Vector3 $center) : void{
		$this->originalCenter = $center;
	}

	/**
	 * @param Position $targetBlock
	 */
	public function pasteCopy(Position $targetBlock) : void{
		$undoBlocks = [];

		foreach($this->copy->getVectors() as $block){
			$v3 = $block->subtract($this->getOriginalCenter());

			$undoBlocks[] = $targetBlock->level->getBlock($targetBlock->add($v3));
			$targetBlock->level->setBlock($targetBlock->add($v3), clone $targetBlock->level->getBlock($block), false);
		}
		$this->session->getRevertStore()->saveRevert(new SyncUndo($undoBlocks, $this->session->getSessionOwner()->getPlayerName()));
	}

	public function resetCopyStorage() : void{
		$this->copy = null;
		$this->originalCenter = null;
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