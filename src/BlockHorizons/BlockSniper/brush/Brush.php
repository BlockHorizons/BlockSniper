<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush;

use BlockHorizons\BlockSniper\events\BrushUseEvent;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\revert\sync\SyncUndo;
use BlockHorizons\BlockSniper\sessions\PlayerSession;
use BlockHorizons\BlockSniper\sessions\Session;
use pocketmine\level\Position;
use pocketmine\math\Vector2;
use pocketmine\Server;

class Brush extends BrushProperties{

	/** @var int */
	public $resetSize = 0;
	/** @var string */
	public $player = "";

	public function __construct(string $player){
		$this->player = $player;
	}

	/**
	 * @return string
	 */
	public function getPlayerName() : string{
		return $this->player;
	}

	/**
	 * @param Session     $session
	 * @param Vector2[][] $plotPoints
	 */
	public function execute(Session $session, array $plotPoints = []) : void{
		$shape = $this->getShape();
		$type = $this->getType();
		if($session instanceof PlayerSession){
			$player = $session->getSessionOwner()->getPlayer();

			Server::getInstance()->getPluginManager()->callEvent($event = new BrushUseEvent($player, $shape, $type));
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

		if($type->canBeExecutedAsynchronously() && $this->size >= $loader->config->asyncOperationSize){
			$shape->editAsynchronously($type, $plotPoints);
		}else{
			$type->setBlocksInside($shape->getBlocksInside());
			$undoBlocks = [];
			foreach($type->fillShape($plotPoints) as $undoBlock){
				$undoBlocks[] = $undoBlock;
			}
			if(count($undoBlocks) === 0){
				return;
			}
			$session->getRevertStore()->saveRevert(new SyncUndo($undoBlocks, $session->getSessionOwner()->getName()));
		}
	}

	/**
	 * @return BaseShape
	 */
	public function getShape() : BaseShape{
		$vector3 = Server::getInstance()->getPlayerExact($this->player)->getTargetBlock(100);

		$location = new Position($vector3->x, $vector3->y, $vector3->z, Server::getInstance()->getPlayer($this->player)->getLevel());
		$shape = new $this->shape(Server::getInstance()->getPlayer($this->player), Server::getInstance()->getPlayer($this->player)->getLevel(), $this->size, $location, $this->hollow);

		return $shape;
	}

	/**
	 * @param \Generator $blocks
	 *
	 * @return BaseType
	 */
	public function getType(\Generator $blocks = null) : BaseType{
		$type = new $this->type(Server::getInstance()->getPlayer($this->player), Server::getInstance()->getPlayer($this->player)->getLevel(), $blocks);

		return $type;
	}

	public function decrement() : void{
		if($this->decrementing){
			if($this->size <= 1){
				/** @var Loader $loader */
				$loader = Server::getInstance()->getPluginManager()->getPlugin("BlockSniper");
				if($loader === null){
					return;
				}
				if($loader->config->resetDecrementBrush){
					$this->size = $this->resetSize;
				}

				return;
			}
			$this->size = $this->size - 1;
		}
	}
}