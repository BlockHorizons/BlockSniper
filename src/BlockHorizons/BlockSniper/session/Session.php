<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\session;

use BlockHorizons\BlockSniper\brush\Brush;
use BlockHorizons\BlockSniper\cloning\CloneStore;
use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\revert\RevertStore;
use BlockHorizons\BlockSniper\session\owner\ISessionOwner;
use BlockHorizons\BlockSniper\session\owner\PlayerSessionOwner;
use BlockHorizons\BlockSniper\session\owner\ServerSessionOwner;
use pocketmine\world\Position;

/**
 * @phpstan-template-covariant TSessionOwner of ISessionOwner
 */
abstract class Session{

	/**
	 * @var ISessionOwner
	 * @phpstan-var TSessionOwner
	 */
	protected $sessionOwner = null;
	/** @var string */
	protected $dataFile = "";
	/** @var Loader */
	protected $loader;

	/** @var Brush */
	protected $brush = null;
	/** @var Selection */
	protected $selection = null;
	/** @var RevertStore */
	protected $revertStore = null;
	/** @var CloneStore */
	protected $cloneStore = null;

	/**
	 * @phpstan-param TSessionOwner $sessionOwner
	 */
	public function __construct(ISessionOwner $sessionOwner, Loader $loader){
		$this->sessionOwner = $sessionOwner;
		$this->revertStore = new RevertStore($loader->config->maxRevertStores);
		$this->cloneStore = new CloneStore($this);
		$this->selection = new Selection();

		$this->loader = $loader;
		if($this->initializeBrush()){
			$loader->getLogger()->debug(Translation::get(Translation::LOG_BRUSH_RESTORED, $this->getSessionOwner()->getName()));
		}
	}

	/**
	 * @return bool
	 */
	protected abstract function initializeBrush() : bool;

	/**
	 * @return Position
	 */
	public abstract function getTargetBlock() : Position;

	/**
	 * @phpstan-return TSessionOwner
	 */
	public function getSessionOwner() : ISessionOwner{
		return $this->sessionOwner;
	}

	/**
	 * @return string
	 */
	public function getDataFile() : string{
		return $this->dataFile;
	}

	/**
	 * @return Brush
	 */
	public function getBrush() : Brush{
		return $this->brush;
	}

	/**
	 * @return RevertStore
	 */
	public function getRevertStore() : RevertStore{
		return $this->revertStore;
	}

	/**
	 * @return CloneStore
	 */
	public function getCloneStore() : CloneStore{
		return $this->cloneStore;
	}

	/**
	 * @return Selection
	 */
	public function getSelection() : Selection{
		return $this->selection;
	}
}