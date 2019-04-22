<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush;

use BlockHorizons\BlockSniper\brush\async\tasks\BrushTask;
use BlockHorizons\BlockSniper\brush\types\TreeType;
use BlockHorizons\BlockSniper\events\BrushUseEvent;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\revert\sync\SyncUndo;
use BlockHorizons\BlockSniper\sessions\PlayerSession;
use BlockHorizons\BlockSniper\sessions\Selection;
use BlockHorizons\BlockSniper\sessions\Session;
use pocketmine\level\Position;
use pocketmine\math\AxisAlignedBB;
use pocketmine\Server;
use function count;

class Brush extends BrushProperties{

	public const MODE_BRUSH = 0;
	public const MODE_SELECTION = 1;

	/** @var bool */
	private $lock = false;

	/**
	 * @param Session        $session
	 * @param Position       $target
	 * @param array          $plotPoints
	 * @param Selection|null $selection
	 *
	 * @return bool
	 */
	public function execute(Session $session, Position $target, array $plotPoints = [], ?Selection $selection = null) : bool{
		if($this->lock){
			// Brush is locked. Return immediately without doing anything.
			return false;
		}
		$this->lock();

		$shape = $this->getShape($selection !== null ? $selection->box() : null, $target);
		$type = ($this->type !== TreeType::class
			? $this->getType($shape->getBlocks($target->getLevel()), $target, $session)
			: new TreeType($this, new Target($target, $target->getLevel())));

		if($session instanceof PlayerSession){
			$player = $session->getSessionOwner()->getPlayer();

			$event = new BrushUseEvent($player, $shape, $type);
			$event->call();
			if($event->isCancelled()){
				return false;
			}
		}
		$this->decrement();

		/** @var Loader $loader */
		$loader = Server::getInstance()->getPluginManager()->getPlugin("BlockSniper");

		$asyncSize = false;
		if(($shape->getBlockCount() ** (1 / 3)) / 2 >= $loader->config->asyncOperationSize){
			$asyncSize = true;
		}

		if($type->canBeExecutedAsynchronously() && $asyncSize){
			$type->setBlocksInside(null);
			Server::getInstance()->getAsyncPool()->submitTask(new BrushTask($this, $session, $shape, $type, $target->getLevel(), $plotPoints));

			return false;
		}

		$undoBlocks = [];
		foreach($type->fillShape($plotPoints) as $undoBlock){
			$undoBlocks[] = $undoBlock;
		}
		if(count($undoBlocks) !== 0){
			$session->getRevertStore()->saveRevert(new SyncUndo($undoBlocks, $session->getSessionOwner()->getName()));
		}

		return true;
	}

	/**
	 * @param AxisAlignedBB|null $bb
	 * @param Position|null      $target
	 *
	 * @return BaseShape
	 */
	public function getShape(AxisAlignedBB $bb = null, Position $target = null) : BaseShape{
		if($target === null){
			$target = new Position();
		}

		return new $this->shape($this, new Target($target, $target->getLevel()), $bb);
	}

	/**
	 * @param \Generator|null $blocks
	 * @param Position|null   $target
	 * @param Session|null    $session
	 *
	 * @return BaseType
	 */
	public function getType(\Generator $blocks = null, Position $target = null, Session $session = null) : BaseType{
		if($target === null){
			$target = new Position();
		}

		return new $this->type($this, new Target($target, $target->getLevel()), $blocks, $session);
	}

	public function decrement() : void{
		if(!$this->decrementing){
			return;
		}
		if($this->size > 1){
			$this->size = $this->size - 1;

			return;
		}
		/** @var Loader $loader */
		$loader = Server::getInstance()->getPluginManager()->getPlugin("BlockSniper");
		if($loader === null){
			return;
		}
		if($loader->config->resetDecrementBrush){
			$this->size = $this->resetSize;
		}
	}

	public function lock() : void{
		$this->lock = true;
	}

	public function unlock() : void{
		$this->lock = false;
	}
}