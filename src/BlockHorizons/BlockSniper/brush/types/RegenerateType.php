<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\revert\async\AsyncUndo;
use BlockHorizons\BlockSniper\sessions\Session;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use pocketmine\level\ChunkManager;
use pocketmine\level\format\Chunk;
use pocketmine\math\Vector3;
use pocketmine\Player;

/*
 * Regenerates the chunk looked at.
 */

class RegenerateType extends BaseType{

	public const ID = self::TYPE_REGENERATE;

	/** @var Session */
	private $session;

	public function __construct(Player $player, ChunkManager $manager, \Generator $blocks = null){
		parent::__construct($player, $manager, $blocks);
		$this->center = $player->getTargetBlock($player->getViewDistance() * 16)->asVector3();
		$this->session = SessionManager::getPlayerSession($player);
	}

	/**
	 * @return \Generator
	 */
	public function fill() : \Generator{
		if($this->myPlotChecked){
			return;
		}
		$x = $this->center->x >> 4;
		$z = $this->center->z >> 4;

		$oldChunk = $this->getLevel()->getChunk($x, $z);
		$c = new Chunk($x, $z);
		$this->getLevel()->setChunk($x, $z, $c);

		$this->getLevel()->populateChunk($x, $z, true);

		$this->session->getRevertStore()->saveRevert(new AsyncUndo([$c], [$oldChunk], $this->session->getSessionOwner()->getName(), $this->level));

		if(false){
			// Make PHP recognize this is a generator.
			yield;
		}
	}

	/**
	 * @return bool
	 */
	public function canBeExecutedAsynchronously() : bool{
		return false;
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Chunk Regenerate";
	}

	/**
	 * @return bool
	 */
	public function canBeHollow() : bool{
		return false;
	}

	/**
	 * @return bool
	 */
	public function usesSize() : bool{
		return false;
	}

	/**
	 * @return bool
	 */
	public function usesBlocks() : bool{
		return false;
	}

	/**
	 * Returns the center of this type.
	 *
	 * @return Vector3
	 */
	public function getCenter() : Vector3{
		return $this->center;
	}
}