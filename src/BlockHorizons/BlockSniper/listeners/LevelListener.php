<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\listeners;

//use BlockHorizons\BlockSniper\git\GitRepository;
use BlockHorizons\BlockSniper\Loader;
use pocketmine\event\level\LevelLoadEvent;
use pocketmine\event\Listener;

class LevelListener implements Listener {

	/** @var Loader */
	private $loader = null;

	public function __construct(Loader $loader) {
		$this->loader = $loader;
	}

	/**
	 * @return Loader
	 */
	public function getLoader(): Loader {
		return $this->loader;
	}

	/**
	 * @param LevelLoadEvent $event
	 */
	public function addProject(LevelLoadEvent $event): void {
		//GitRepository::addProject($event->getLevel());
	}
}