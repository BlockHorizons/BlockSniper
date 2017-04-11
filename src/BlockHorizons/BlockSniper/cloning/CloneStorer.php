<?php

namespace BlockHorizons\BlockSniper\cloning;

use BlockHorizons\BlockSniper\Loader;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;

class CloneStorer {
	
	private $copyStore = [];
	private $originalCenter = null;
	private $target = null;
	private $loader;
	
	public function __construct(Loader $loader) {
		$this->loader = $loader;
	}
	
	public function setTargetBlock(Vector3 $target) {
		$this->target = $target;
	}
	
	/**
	 * @param array $blocks
	 */
	public function saveCopy(array $blocks) {
		$i = 0;
		$this->unsetCopy();
		foreach($blocks as $block) {
			$this->copyStore[$block->getId() . ":" . $block->getDamage() . "(" . $i . ")"] = [
				"x" => $block->x - $this->getOriginalCenter()->x,
				"y" => $block->y - $this->getOriginalCenter()->y,
				"z" => $block->z - $this->getOriginalCenter()->z
			];
			$i++;
		}
		unset($i);
	}
	
	public function unsetCopy() {
		unset($this->copyStore);
		$this->copyStore = [];
	}
	
	// Required for math to copy-paste it on the location looked at.
	
	public function getOriginalCenter(): Vector3 {
		return $this->originalCenter;
	}
	
	public function setOriginalCenter(Vector3 $center) {
		$this->originalCenter = $center;
	}
	
	public function pasteCopy(Level $level, Player $player) {
		$undoBlocks = [];
		foreach($this->copyStore as $key => $block) {
			$Id = explode("(", $key);
			$blockId = $Id[0];
			$meta = explode(":", $blockId);
			$meta = $meta[1];
			$x = $block["x"];
			$y = $block["y"] + 1;
			$z = $block["z"];
			$finalBlock = Item::get($blockId)->getBlock();
			$finalBlock->setDamage((int)$meta !== null ? $meta : 0);
			
			$blockPos = new Vector3($x + $this->getTargetBlock()->x, $y + $this->getTargetBlock()->y, $z + $this->getTargetBlock()->z);
			$undoBlocks[] = $level->getBlock($blockPos);
			$level->setBlock($blockPos, Block::get((int)$blockId, (int)$meta), false, false);
		}
		$this->getLoader()->getUndoStore()->saveUndo($undoBlocks, $player);
	}
	
	public function getTargetBlock(): Vector3 {
		return $this->target;
	}
	
	/**
	 * @return Loader
	 */
	public function getLoader(): Loader {
		return $this->loader;
	}
	
	public function resetCopyStorage() {
		$this->copyStore = [];
		$this->originalCenter = null;
		$this->target = null;
	}
	
	/**
	 * @return bool
	 */
	public function copyStoreExists() {
		if(!is_array($this->copyStore) || empty($this->copyStore)) {
			return false;
		}
		return true;
	}
	
	/**
	 * @return int
	 */
	public function getCopyBlockAmount() {
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
	 * @return bool
	 */
	public function saveTemplate(string $templateName, array $blocks, Vector3 $targetBlock): bool {
		$template = [];
		$i = 0;
		foreach($blocks as $block) {
			$template[$block->getId() . ":" . $block->getDamage() . "(" . $i . ")"] = [
				"x" => $block->x - $targetBlock->x,
				"y" => $block->y - $targetBlock->y,
				"z" => $block->z - $targetBlock->z
			];
			$i++;
		}
		unset($i);
		file_put_contents($this->getLoader()->getDataFolder() . "templates/" . $templateName . ".yml", serialize($template));
		return true;
	}
	
	/**
	 * @param string $templateName
	 * @param Block  $targetBlock
	 *
	 * @return bool
	 */
	public function pasteTemplate(string $templateName, Block $targetBlock, Player $player): bool {
		$data = file_get_contents($this->getLoader()->getDataFolder() . "templates/" . $templateName . ".yml");
		$content = unserialize($data);
		
		$undoBlocks = [];
		
		foreach($content as $key => $block) {
			$Id = explode("(", $key);
			$blockId = $Id[0];
			$meta = explode(":", $blockId);
			$meta = $meta[1];
			$x = $block["x"];
			$y = $block["y"] + 1;
			$z = $block["z"];
			$finalBlock = Item::get($blockId)->getBlock();
			$finalBlock->setDamage((int)$meta !== null ? $meta : 0);
			
			$blockPos = new Vector3($x + $targetBlock->x, $y + $targetBlock->y, $z + $targetBlock->z);
			$undoBlocks[] = $targetBlock->getLevel()->getBlock($blockPos);
			$targetBlock->getLevel()->setBlock($blockPos, Block::get((int)$blockId, (int)$meta), false, false);
		}
		$this->getLoader()->getUndoStore()->saveUndo($undoBlocks, $player);
		return true;
	}
	
	public function templateExists(string $templateName): bool {
		if(is_file($this->getLoader()->getDataFolder() . "templates/" . $templateName . ".yml")) {
			return true;
		}
		return false;
	}
}