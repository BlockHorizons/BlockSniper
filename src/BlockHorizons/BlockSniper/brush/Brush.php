<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush;

use BlockHorizons\BlockSniper\brush\async\tasks\BrushTask;
use BlockHorizons\BlockSniper\brush\types\TreeType;
use BlockHorizons\BlockSniper\events\BrushUseEvent;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\revert\SyncRevert;
use BlockHorizons\BlockSniper\sessions\PlayerSession;
use BlockHorizons\BlockSniper\sessions\Selection;
use BlockHorizons\BlockSniper\sessions\Session;
use pocketmine\math\AxisAlignedBB;
use pocketmine\Server;
use pocketmine\world\Position;
use function count;

/**
 * Class Brush implements the main brushing logic of BlockSniper. It handles the selection of the brush shape and type
 * and fills the selection made using the type selected. It also decides if a shape is filled asynchronously or
 * synchronously, depending on its size.
 */
class Brush extends BrushProperties{

	/**
	 * MODE_BRUSH is the brush mode for classic brushing, meaning actually brushing from a long distance.
	 */
	public const MODE_BRUSH = 0;
	/**
	 * MODE_SELECTION is the brush mode for selection based world edit, much like classic WorldEdit. Selections must be
	 * made using the selection item.
	 */
	public const MODE_SELECTION = 1;

	/** @var bool */
	private $lock = false;

	/**
	 * execute executes the brush, using $session as the user of the brush. $target is assumed to be the target of the
	 * brush, which will form the centre of the brush Shape if the brush mode is set to MODE_BRUSH. If $plotPoints is
	 * not empty, blocks will only ever be placed within the points passed.
	 * If the brush mode of the brush is MODE_SELECTION, the selection $selection must be passed, defining the bounds of
	 * the selection.
	 *
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
			? $this->getType($shape->getBlocks($target->getWorld()), $target, $session)
			: new TreeType($this, new Target($target, $target->getWorld())));

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
			Server::getInstance()->getAsyncPool()->submitTask(new BrushTask($this, $session, $shape, $type, $target->getWorld(), $plotPoints));

			return false;
		}

		$undoBlocks = [];
		foreach($type->fillShape($plotPoints) as $undoBlock){
			$undoBlocks[] = $undoBlock;
		}
		if(count($undoBlocks) !== 0){
			$session->getRevertStore()->saveUndo(new SyncRevert($undoBlocks, $target->getWorld()));
		}

		return true;
	}

	/**
	 * getShape gets a Shape using a selection and a target position. Each of these may be null to create a Shape
	 * that has no functionality but allows calling methods specific to a Shape.
	 *
	 * @param AxisAlignedBB|null $bb
	 * @param Position|null      $target
	 *
	 * @return Shape
	 */
	public function getShape(AxisAlignedBB $bb = null, Position $target = null) : Shape{
		if($target === null){
			$target = new Position();
		}

		return new $this->shape($this, new Target($target, $target->getWorld()), $bb);
	}

	/**
	 * getType gets a Type using a block generator, target position and session passed. Each of these may be null to
	 * create a Type that has no functionality but allows calling methods specific to a Type.
	 *
	 * @param \Generator|null $blocks
	 * @param Position|null   $target
	 * @param Session|null    $session
	 *
	 * @return Type
	 */
	public function getType(\Generator $blocks = null, Position $target = null, Session $session = null) : Type{
		if($target === null){
			$target = new Position();
		}

		return new $this->type($this, new Target($target, $target->getWorld()), $blocks, $session);
	}

	/**
	 * lock locks the Brush, making it unusable until it is unlocked using unlock(). Attempts to brush using this Brush
	 * instance will be ignored while locked.
	 */
	public function lock() : void{
		$this->lock = true;
	}

	/**
	 * unlock unlocks the Brush, making it usable again after being locked using lock().
	 */
	public function unlock() : void{
		$this->lock = false;
	}
}