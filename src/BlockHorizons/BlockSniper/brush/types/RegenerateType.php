<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BrushProperties;
use BlockHorizons\BlockSniper\brush\Target;
use BlockHorizons\BlockSniper\brush\Type;
use BlockHorizons\BlockSniper\revert\async\AsyncUndo;
use BlockHorizons\BlockSniper\sessions\Session;
use pocketmine\level\format\Chunk;

/*
 * Regenerates the chunk looked at.
 */

class RegenerateType extends Type{

	public const ID = self::TYPE_REGENERATE;

	/** @var Session */
	private $session;

	public function __construct(BrushProperties $properties, Target $target, \Generator $blocks = null, Session $session = null){
		parent::__construct($properties, $target, $blocks);
		$this->session = $session;
	}

	/**
	 * @return \Generator
	 */
	public function fill() : \Generator{
		if($this->myPlotChecked){
			return;
		}
		$x = $this->target->x >> 4;
		$z = $this->target->z >> 4;

		$oldChunk = $this->chunkManager->getChunk($x, $z);
		$c = new Chunk($x, $z);
		$this->chunkManager->setChunk($x, $z, $c);

		$this->chunkManager->populateChunk($x, $z, true);

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
	public function usesBrushBlocks() : bool{
		return false;
	}
}