<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush;

use BlockHorizons\BlockSniper\brush\types\TreeType;
use BlockHorizons\BlockSniper\events\BrushUseEvent;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\revert\sync\SyncUndo;
use BlockHorizons\BlockSniper\sessions\PlayerSession;
use BlockHorizons\BlockSniper\sessions\Selection;
use BlockHorizons\BlockSniper\sessions\Session;
use pocketmine\math\AxisAlignedBB;
use pocketmine\Player;
use pocketmine\Server;

class Brush extends BrushProperties{

	public const MODE_BRUSH = 0;
	public const MODE_SELECTION = 1;

	/** @var string */
	public $player = "";

	public function __construct(string $playerName){
		parent::__construct();
		$this->player = $playerName;
	}

	/**
	 * @return string
	 */
	public function getPlayerName() : string{
		return $this->player;
	}

	/**
	 * @return null|Player
	 */
	public function getPlayer() : ?Player{
		return Server::getInstance()->getPlayer($this->player);
	}

	/**
	 * @param Session        $session
	 * @param Selection|null $selection
	 * @param array          $plotPoints
	 */
	public function execute(Session $session, ?Selection $selection, array $plotPoints = []) : void{
		$shape = $this->getShape($selection !== null ? $selection->box() : null);
		$type = ($this->type !== TreeType::class
			? $this->getType($shape->getBlocksInside())
			: new TreeType($this->getPlayer(), $this->getPlayer()->getLevel()));

		if($session instanceof PlayerSession){
			$player = $session->getSessionOwner()->getPlayer();

			$event = new BrushUseEvent($player, $shape, $type);
			$event->call();
			if($event->isCancelled()){
				return;
			}
		}
		$this->decrement();

		/** @var Loader $loader */
		$loader = Server::getInstance()->getPluginManager()->getPlugin("BlockSniper");
		if($loader === null){
			return;
		}

		$asyncSize = false;
		if($selection !== null) {
			if($selection->blockCount() ** 1/3 >= $loader->config->asyncOperationSize) {
				$asyncSize = true;
			}
		} elseif($this->size >= $loader->config->asyncOperationSize) {
			$asyncSize = true;
		}

		if($type->canBeExecutedAsynchronously() && $asyncSize){
			$type->setBlocksInside(null);
			$shape->editAsynchronously($type, $plotPoints);
			return;
		}
		$undoBlocks = [];
		foreach($type->fillShape($plotPoints) as $undoBlock){
			$undoBlocks[] = $undoBlock;
		}
		if(count($undoBlocks) === 0){
			return;
		}
		$session->getRevertStore()->saveRevert(new SyncUndo($undoBlocks, $session->getSessionOwner()->getName()));
	}

	/**
	 * @param null|AxisAlignedBB $bb
	 *
	 * @return BaseShape
	 */
	public function getShape(AxisAlignedBB $bb = null) : BaseShape{
		$player = $this->getPlayer();

		return new $this->shape($player, $player->getLevel(), $player->getTargetBlock(100)->asPosition(), $bb, $this);
	}

	/**
	 * @param \Generator $blocks
	 *
	 * @return BaseType
	 */
	public function getType(\Generator $blocks = null) : BaseType{
		return new $this->type($this->getPlayer(), $this->getPlayer()->getLevel(), $blocks);
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
}