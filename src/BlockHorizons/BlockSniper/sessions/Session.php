<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\sessions;

use BlockHorizons\BlockSniper\brush\Brush;
use BlockHorizons\BlockSniper\cloning\CloneStorer;
use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\revert\RevertStorer;
use BlockHorizons\BlockSniper\sessions\owners\ISessionOwner;
use BlockHorizons\BlockSniper\sessions\owners\PlayerSessionOwner;
use BlockHorizons\BlockSniper\sessions\owners\ServerSessionOwner;

abstract class Session{

	/** @var ISessionOwner */
	protected $sessionOwner = null;
	/** @var string */
	protected $dataFile = "";
	/** @var Brush */
	protected $brush = null;
	/** @var RevertStorer */
	protected $revertStorer = null;
	/** @var CloneStorer */
	protected $cloneStorer = null;
	/** @var Loader */
	protected $loader;

	public function __construct(ISessionOwner $sessionOwner, Loader $loader){
		$this->sessionOwner = $sessionOwner;
		$this->revertStorer = new RevertStorer($loader->config->maxRevertStores);
		$this->cloneStorer = new CloneStorer($this, $loader->getDataFolder());
		$this->loader = $loader;
		if($this->initializeBrush()){
			$loader->getLogger()->debug(Translation::get(Translation::LOG_BRUSH_RESTORED, [$this->getSessionOwner()->getName()]));
		}
	}

	/**
	 * @return bool
	 */
	protected abstract function initializeBrush() : bool;

	/**
	 * @return PlayerSessionOwner|ServerSessionOwner
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
	 * @return RevertStorer
	 */
	public function getRevertStorer() : RevertStorer{
		return $this->revertStorer;
	}

	/**
	 * @return CloneStorer
	 */
	public function getCloneStorer() : CloneStorer{
		return $this->cloneStorer;
	}
}