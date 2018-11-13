<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\cloning;

use BlockHorizons\BlockSniper\brush\async\tasks\PasteTask;
use BlockHorizons\BlockSniper\brush\BaseShape;
use BlockHorizons\BlockSniper\revert\sync\SyncUndo;
use BlockHorizons\BlockSniper\sessions\Session;
use pocketmine\block\Block;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Server;

class CloneStore{

	/** @var BaseShape */
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
	 * @param BaseShape $generator
	 */
	public function saveCopy(BaseShape $generator) : void{
		$this->copy = $generator;
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

		foreach($this->copy->getBlocksInside(true) as $block){
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
	 * @param string  $templateName
	 * @param \Generator   $blocks
	 * @param Vector3 $targetBlock
	 *
	 * @deprecated
	 */
	public function saveTemplate(string $templateName, \Generator $blocks, Vector3 $targetBlock) : void{
		$template = [];
		$i = 0;
		foreach($blocks as $block){
			$template[$block->getId() . ":" . $block->getDamage() . "(" . $i . ")"] = [
				"x" => $block->x - $targetBlock->x,
				"y" => $block->y - $targetBlock->y,
				"z" => $block->z - $targetBlock->z
			];
			$i++;
		}
		file_put_contents($this->path . "templates/" . $templateName . ".template", serialize($template));
	}

	/**
	 * @param string   $templateName
	 * @param Position $targetBlock
	 *
	 * @deprecated
	 */
	public function pasteTemplate(string $templateName, Position $targetBlock) : void{
		$data = file_get_contents($this->path . "templates/" . $templateName . ".yml");
		$content = unserialize($data, ["allowed_classes" => false]);

		$undoBlocks = [];

		foreach($content as $key => $block){
			$Id = explode("(", $key);
			$blockData = $Id[0];
			$meta = explode(":", $blockData);
			$blockId = (int) $meta[0];
			$meta = (int) $meta[1];
			$x = (int) $block["x"];
			$y = (int) $block["y"] + 1;
			$z = (int) $block["z"];

			$blockPos = new Vector3($x + $targetBlock->x, $y + $targetBlock->y, $z + $targetBlock->z);
			$undoBlocks[] = $targetBlock->getLevel()->getBlock($blockPos);
			$targetBlock->getLevel()->setBlock($blockPos, Block::get($blockId, $meta), false);
		}
		$this->session->getRevertStore()->saveRevert(new SyncUndo($undoBlocks, $this->session->getSessionOwner()->getPlayerName()));
	}

	/**
	 * @param string $templateName
	 *
	 * @return bool
	 *
	 * @deprecated
	 */
	public function templateExists(string $templateName) : bool{
		if(is_file($this->path . "templates/" . $templateName . ".yml")){
			return true;
		}

		return false;
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