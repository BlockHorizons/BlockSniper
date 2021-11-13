<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\type;

use BlockHorizons\BlockSniper\brush\BrushProperties;
use BlockHorizons\BlockSniper\brush\Target;
use BlockHorizons\BlockSniper\brush\Type;
use BlockHorizons\BlockSniper\revert\AsyncRevert;
use BlockHorizons\BlockSniper\session\owner\ISessionOwner;
use BlockHorizons\BlockSniper\session\Session;
use BlockHorizons\BlockSniper\session\SessionManager;
use BlockHorizons\BlockSniper\task\CooldownBarTask;
use Generator;
use pocketmine\block\Block;
use pocketmine\Server;
use pocketmine\world\ChunkLockId;
use pocketmine\world\format\Chunk;
use pocketmine\world\generator\PopulationTask;
use pocketmine\world\World;

/*
 * Regenerates the chunk looked at.
 */

class RegenerateType extends Type{

	/** @var Session<ISessionOwner> */
	private $session;

	/**
	 * @phpstan-param Generator<int, Block, void, void>|null  $blocks
	 * @phpstan-param Session<ISessionOwner>|null             $session
	 */
	public function __construct(BrushProperties $properties, Target $target, Generator $blocks = null, Session $session = null){
		parent::__construct($properties, $target, $blocks);
		$this->session = $session;
	}

	public function fill() : Generator{
		if($this->myPlotChecked){
			return;
		}
		$x = $this->target->getFloorX() >> Chunk::COORD_BIT_SIZE;
		$z = $this->target->getFloorZ() >> Chunk::COORD_BIT_SIZE;

		$oldChunk = $this->chunkManager->getChunk($x, $z);
		if($oldChunk === null){
			return;
		}

		$chunkLockId = new ChunkLockId();
		for($xOffset = -1; $xOffset <= 1; ++$xOffset){
			for($zOffset = -1; $zOffset <= 1; ++$zOffset){
				//Forcibly remove any existing lock. This tells any other running tasks that were using this chunk to get lost.
				$this->chunkManager->unlockChunk($x + $xOffset, $z + $zOffset, null);

				//Lock the chunk for our usage.
				$this->chunkManager->lockChunk($x + $xOffset, $z + $zOffset, $chunkLockId);
			}
		}

		$task = new PopulationTask(
			$this->chunkManager->getId(),
			$x,
			$z,
			$oldChunk,
			$this->chunkManager->getAdjacentChunks($x, $z),
			function(Chunk $centerChunk, array $adjacentChunks) use ($x, $z, $chunkLockId, $oldChunk) : void{
				$dirtyChunks = 0;
				for($xOffset = -1; $xOffset <= 1; ++$xOffset){
					for($zOffset = -1; $zOffset <= 1; ++$zOffset){
						if(!$this->chunkManager->unlockChunk($x + $xOffset, $z + $zOffset, $chunkLockId)){
							// Someone else modified this chunk in the meantime. This could be because of a player
							// placing a block, or another plugin did something with the chunk.
							$dirtyChunks++;
						}
					}
				}

				if($dirtyChunks > 0){
					// One of our chunks was modified while we were working on it, so we can't continue.
					// TODO: reschedule? Though I guess this should be quite a rare incident anyway ...

				}else{
					$this->chunkManager->setChunk($x, $z, $centerChunk);
					foreach($adjacentChunks as $hash => $chunk){
						World::getXZ($hash, $xAdjacent, $zAdjacent);
						$this->chunkManager->setChunk($xAdjacent, $zAdjacent, $chunk);
					}

					//TODO: cooldown
					$this->session->getRevertStore()->saveUndo(new AsyncRevert([$centerChunk], [$oldChunk], $this->chunkManager));
				}
			}
		);
		$this->chunkManager->getServer()->getAsyncPool()->submitTask($task);

		// Make PHP recognize this is a generator.
		yield from [];
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