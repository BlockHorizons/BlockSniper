<?php

namespace BlockHorizons\BlockSniper\cloning;

use BlockHorizons\BlockSniper\Loader;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;

class CloneStorer {
	
	private $copyStore = [];
	private $originalCenter = null;
	private $loader;
	
	public function __construct(Loader $loader) {
		$this->loader = $loader;
	}
	
	/**
	 * @param Block[] $blocks
	 */
	public function saveCopy(array $blocks, Player $player) {
		$this->unsetCopy($player);
		foreach($blocks as $block) {
			$this->copyStore[$player->getName()][] = $block->subtract($this->getOriginalCenter($player));
		}
	}

	/**
	 * @param Player $player
	 */
	public function unsetCopy(Player $player) {
		unset($this->copyStore[$player->getName()]);
	}

	/**
	 * @param Player $player
	 *
	 * @return Vector3
	 */
	public function getOriginalCenter(Player $player): Vector3 {
		return $this->originalCenter[$player->getName()];
	}

	/**
	 * @param Vector3 $center
	 * @param Player  $player
	 */
	public function setOriginalCenter(Vector3 $center, Player $player) {
		$this->originalCenter[$player->getName()] = $center;
	}

	/**
	 * @param Player $player
	 */
	public function pasteCopy(Player $player) {
		$undoBlocks = [];
		$level = $player->getLevel();
		$center = $player->getTargetBlock(100);
		foreach($this->copyStore[$player->getName()] as $block) {
			$undoBlocks[] = $level->getBlock($center->add($block));
			$level->setBlock($center->add($block), $block, false, false);
		}
		$this->getLoader()->getUndoStorer()->saveUndo($undoBlocks, $player);
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
	 * @param Player $player
	 *
	 * @return bool
	 */
	public function copyStoreExists(Player $player): bool {
		if(!is_array($this->copyStore[$player->getName()]) || empty($this->copyStore[$player->getName()])) {
			return false;
		}
		return true;
	}
	
	/**
	 * @return int
	 */
	public function getCopyBlockAmount(Player $player): int {
		return count($this->copyStore[$player->getName()]);
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
		$this->getLoader()->getUndoStorer()->saveUndo($undoBlocks, $player);
		return true;
	}
	
	public function templateExists(string $templateName): bool {
		if(is_file($this->getLoader()->getDataFolder() . "templates/" . $templateName . ".yml")) {
			return true;
		}
		return false;
	}
}