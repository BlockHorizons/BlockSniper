<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush;

use BlockHorizons\BlockSniper\brush\types\TreeType;
use BlockHorizons\BlockSniper\events\BrushUseEvent;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\revert\sync\SyncUndo;
use BlockHorizons\BlockSniper\sessions\PlayerSession;
use BlockHorizons\BlockSniper\sessions\Session;
use pocketmine\math\Vector2;
use pocketmine\Player;
use pocketmine\Server;

class Brush extends BrushProperties{

	/** @var int */
	public $resetSize = 0;
	/** @var string */
	public $player = "";

	public function __construct(string $player){
		parent::__construct();
		$this->player = $player;
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
	 * @param Session     $session
	 * @param Vector2[][] $plotPoints
	 */
	public function execute(Session $session, array $plotPoints = []) : void{
		$shape = $this->getShape();
		if($this->type !== TreeType::class){
			$type = $this->getType($shape->getBlocksInside());
		}else{
			$type = new TreeType($this->getPlayer(), $this->getPlayer()->getLevel());
		}

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
			$type->setBlocksInside(null);
			$shape->editAsynchronously($type, $plotPoints);
		}else{
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
		$player = $this->getPlayer();

		return new $this->shape($player, $player->getLevel(), $this->size, $player->getTargetBlock(100)->asPosition(), $this->hollow);
	}

	/**
	 * @param \Generator $blocks
	 *
	 * @return BaseType
	 */
	public function getType(\Generator $blocks) : BaseType{
		return new $this->type($this->getPlayer(), $this->getPlayer()->getLevel(), $blocks);
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