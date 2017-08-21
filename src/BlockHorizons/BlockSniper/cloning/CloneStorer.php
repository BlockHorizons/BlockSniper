<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\cloning;

use BlockHorizons\BlockSniper\brush\async\tasks\PasteTask;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\sessions\Session;
use BlockHorizons\BlockSniper\undo\sync\SyncUndo;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;

class CloneStorer {

	/** @var Block[] */
	private $copyStore = [];
	/** @var Vector3 */
	private $originalCenter = null;
	/** @var Loader */
	private $loader = null;

	public function __construct(Session $session) {
		$this->session = $session;
	}

	/**
	 * @param Block[] $blocks
	 */
	public function saveCopy(array $blocks) {
		$this->unsetCopy();
		foreach($blocks as $block) {
			$block->subtract($this->getOriginalCenter());
			$this->copyStore[] = $block;
		}
	}

	public function unsetCopy() {
		unset($this->copyStore);
	}

	/**
	 * @return Vector3
	 */
	public function getOriginalCenter(): Vector3 {
		return $this->originalCenter;
	}

	/**
	 * @param Vector3 $center
	 */
	public function setOriginalCenter(Vector3 $center) {
		$this->originalCenter = $center;
	}

	/**
	 * @param Position $targetBlock
	 */
	public function pasteCopy(Position $targetBlock) {
		$undoBlocks = [];
		foreach($this->copyStore as $block) {
			$undoBlocks[] = $targetBlock->level->getBlock($targetBlock->add($block));
			$targetBlock->level->setBlock($targetBlock->add($block), $block, false, false);
		}
		$this->session->getRevertStorer()->saveRevert(new SyncUndo($undoBlocks, $this->session->getSessionOwner()->getPlayerName()));
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
	}

	/**
	 * @param Player $player
	 *
	 * @return bool
	 */
	public function copyStoreExists(): bool {
		return !empty($this->copyStore);
	}

	/**
	 * @param Player $player
	 *
	 * @return int
	 */
	public function getCopyBlockAmount(): int {
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
	 *
	 * @deprecated
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
		file_put_contents($this->getLoader()->getDataFolder() . "templates/" . $templateName . ".yml", serialize($template));
		return true;
	}

	/**
	 * @param string $templateName
	 * @param Block  $targetBlock
	 * @param Player $player
	 *
	 * @deprecated
	 * @return bool
	 */
	public function pasteTemplate(string $templateName, Position $targetBlock): bool {
		$data = file_get_contents($this->getLoader()->getDataFolder() . "templates/" . $templateName . ".yml");
		$content = unserialize($data);

		$undoBlocks = [];

		foreach($content as $key => $block) {
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
		$this->session->getRevertStorer()->saveRevert(new SyncUndo($undoBlocks, $this->session->getSessionOwner()->getPlayerName()));
		return true;
	}

	/**
	 * @param string $templateName
	 *
	 * @return bool
	 *
	 * @deprecated
	 */
	public function templateExists(string $templateName): bool {
		if(is_file($this->getLoader()->getDataFolder() . "templates/" . $templateName . ".yml")) {
			return true;
		}
		return false;
	}

	/**
	 * @param string  $file
	 * @param Vector3 $center
	 * @param array   $chunks
	 * @param Player  $player
	 */
	public function pasteSchematic(string $file, Vector3 $center, array $chunks) {
		$this->getLoader()->getServer()->getScheduler()->scheduleAsyncTask(new PasteTask($file, $center, $chunks, $this->session->getSessionOwner()->getName()));
	}
}