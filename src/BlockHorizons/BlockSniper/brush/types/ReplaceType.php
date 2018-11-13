<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use pocketmine\level\ChunkManager;
use pocketmine\Player;

/*
 * Replaces the obsolete blocks within the brush radius.
 */

class ReplaceType extends BaseType{

	public const ID = self::TYPE_REPLACE;

	public function __construct(Player $player, ChunkManager $level, \Generator $blocks){
		parent::__construct($player, $level, $blocks);
		$this->obsolete = SessionManager::getPlayerSession($player)->getBrush()->getObsolete();
	}

	/**
	 * @return \Generator
	 */
	public function fill() : \Generator{
		foreach($this->blocks as $block){
			$randomBlock = $this->brushBlocks[array_rand($this->brushBlocks)];
			foreach($this->obsolete as $obsolete){
				if($block->getId() === $obsolete->getId() and $block->getDamage() === $obsolete->getDamage()){
					yield $block;
					$this->putBlock($block, $randomBlock->getId(), $randomBlock->getDamage());
				}
			}
		}
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Replace";
	}

	/**
	 * Returns the obsolete blocks of this type.
	 *
	 * @return \Generator
	 */
	public function getObsolete() : \Generator{
		return $this->obsolete;
	}
}

