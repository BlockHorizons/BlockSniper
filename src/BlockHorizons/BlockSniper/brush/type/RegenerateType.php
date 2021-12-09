<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\type;

use BlockHorizons\BlockSniper\brush\BrushProperties;
use BlockHorizons\BlockSniper\brush\Target;
use BlockHorizons\BlockSniper\brush\Type;
use BlockHorizons\BlockSniper\revert\AsyncRevert;
use BlockHorizons\BlockSniper\session\owner\ISessionOwner;
use BlockHorizons\BlockSniper\session\Session;
use Generator;
use pocketmine\block\Block;
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
		if(!$this->chunkManager instanceof World || $this->myPlotChecked){
			return;
		}
		$world = $this->chunkManager;
		$x = $this->target->getFloorX() >> Chunk::COORD_BIT_SIZE;
		$z = $this->target->getFloorZ() >> Chunk::COORD_BIT_SIZE;

		$oldChunk = $world->getChunk($x, $z);
		if($oldChunk === null){
			return;
		}

		$chunkLockId = new ChunkLockId();
		for($xOffset = -1; $xOffset <= 1; ++$xOffset){
			for($zOffset = -1; $zOffset <= 1; ++$zOffset){
				//Forcibly remove any existing lock. This tells any other running tasks that were using this chunk to get lost.
				$world->unlockChunk($x + $xOffset, $z + $zOffset, null);

				//Lock the chunk for our usage.
				$world->lockChunk($x + $xOffset, $z + $zOffset, $chunkLockId);
			}
		}

		//TODO: this is one big awful hack which is only necessary because 4.0 doesn't support any simple way to mark a
		//chunk for regeneration :(

		$asyncPool = $world->getServer()->getAsyncPool();
		$worker = $asyncPool->selectWorker();

		//this is costly, but we have no way to know if the worker has been primed for generation or not
		//TODO: remove this hack when 4.x supports a simple way to regenerate chunks
		$world->registerGeneratorToWorker($worker);

		$asyncPool->submitTaskToWorker(new PopulationTask(
			$world->getId(),
			$x,
			$z,
			null,
			$world->getAdjacentChunks($x, $z),
			function(Chunk $centerChunk, array $adjacentChunks) use ($world, $x, $z, $chunkLockId, $oldChunk) : void{
				if(!$world->isLoaded()){
					return;
				}
				$dirtyChunks = 0;
				for($xOffset = -1; $xOffset <= 1; ++$xOffset){
					for($zOffset = -1; $zOffset <= 1; ++$zOffset){
						if(!$world->unlockChunk($x + $xOffset, $z + $zOffset, $chunkLockId)){
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
					$world->setChunk($x, $z, $centerChunk);
					foreach($adjacentChunks as $hash => $chunk){
						World::getXZ($hash, $xAdjacent, $zAdjacent);
						$world->setChunk($xAdjacent, $zAdjacent, $chunk);
					}

					//TODO: cooldown
					$centerChunkHash = World::chunkHash($x, $z);
					$this->session->getRevertStore()->saveUndo(new AsyncRevert([$centerChunkHash => $centerChunk], [$centerChunkHash => $oldChunk], $world));
				}
			}
		), $worker);

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