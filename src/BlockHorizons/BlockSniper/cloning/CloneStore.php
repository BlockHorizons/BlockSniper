<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\cloning;

use BlockHorizons\BlockSniper\brush\async\tasks\PasteTask;
use BlockHorizons\BlockSniper\revert\sync\SyncUndo;
use BlockHorizons\BlockSniper\sessions\Session;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Server;

class CloneStore{

	/** @var Block[] */
	private $copyStore = [];
	/** @var Vector3 */
	private $originalCenter = null;
	/** @var string */
	private $path = "";

	public function __construct(Session $session, string $path){
		$this->session = $session;
		$this->path = $path;
	}

	/**
	 * @param Block[] $blocks
	 */
	public function saveCopy(array $blocks) : void{
		$this->unsetCopy();
		foreach($blocks as $block){
			$v3 = $block->subtract($this->getOriginalCenter());
			$block->setComponents($v3->x, $v3->y, $v3->z);
			$this->copyStore[] = $block;
		}
	}

	public function unsetCopy() : void{
		unset($this->copyStore);
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
		foreach($this->copyStore as $block){
			$undoBlocks[] = $targetBlock->level->getBlock($targetBlock->add($block));
			$targetBlock->level->setBlock($targetBlock->add($block), $block, false, false);
		}
		$this->session->getRevertStore()->saveRevert(new SyncUndo($undoBlocks, $this->session->getSessionOwner()->getPlayerName()));
	}

	public function resetCopyStorage() : void{
		$this->copyStore = [];
		$this->originalCenter = null;
	}

	/**
	 * @return bool
	 */
	public function copyStoreExists() : bool{
		return !empty($this->copyStore);
	}

	/**
	 * @return int
	 */
	public function getCopyBlockAmount() : int{
		return count($this->copyStore);
	}

	/*
	 * Templates
	 */

	/**
	 * @param string  $templateName
	 * @param array   $blocks
	 * @param Vector3 $targetBlock
	 *
	 * @deprecated
	 */
	public function saveTemplate(string $templateName, array $blocks, Vector3 $targetBlock) : void{
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
			$finalBlock = Item::get($blockId)->getBlock();
			$finalBlock->setDamage(!empty($meta) ? $meta : 0);

			$blockPos = new Vector3($x + $targetBlock->x, $y + $targetBlock->y, $z + $targetBlock->z);
			$undoBlocks[] = $targetBlock->getLevel()->getBlock($blockPos);
			$targetBlock->getLevel()->setBlock($blockPos, Block::get($blockId, $meta), false, false);
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