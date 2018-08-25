<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniperTests;

abstract class Test{

	/** @var Loader */
	protected $loader = null;

	public function __construct(Loader $loader){
		$this->loader = $loader;
	}

	/**
	 * @return Loader
	 */
	public function getLoader() : Loader{
		return $this->loader;
	}

	/**
	 * @return \BlockHorizons\BlockSniper\Loader|null
	 */
	public function getBlockSniper() : ?\BlockHorizons\BlockSniper\Loader{
		$plugin = $this->loader->getServer()->getPluginManager()->getPlugin("BlockSniper");
		if(!($plugin instanceof \BlockHorizons\BlockSniper\Loader)){
			return null;
		}

		return $plugin;
	}

	/**
	 * @return bool
	 */
	public function run() : bool{
		try{
			$this->getBlockSniper()->getLogger()->info("Test " . (new \ReflectionClass(static::class))->getShortName() . " is now running...");
			$return = $this->onRun();
			$this->getBlockSniper()->getLogger()->info("Test " . (new \ReflectionClass(static::class))->getShortName() . " finished with return value: " . ($return ? "Succeeded" : "Failed"));
		}catch(\ReflectionException $exception){
			$return = false;
		}

		return $return;
	}

	/**
	 * @return bool
	 */
	protected abstract function onRun() : bool;
}