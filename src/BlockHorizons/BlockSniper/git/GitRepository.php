<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\git;

use BlockHorizons\BlockSniper\Loader;
use pocketmine\level\Level;

class GitRepository {

	/** @var Project[] */
	private static $projects = [];

	public function __construct(Loader $loader) {
		self::$projects[0] = new Project($loader->getServer()->getDefaultLevel());
	}

	/**
	 * @param Level $level
	 *
	 * @return bool
	 */
	public static function addProject(Level $level): bool {
		if(isset(self::$projects[$level->getId()])) {
			return false;
		}
		self::$projects[$level->getId()] = new Project($level);
		return true;
	}

	/**
	 * @param int $projectId
	 *
	 * @return bool
	 */
	public static function closeProject(int $projectId): bool {
		if(!isset(self::$projects[$projectId])) {
			return false;
		}
		unset(self::$projects[$projectId]);
		return true;
	}

	/**
	 * @param int $projectId
	 *
	 * @return bool
	 */
	public static function projectExists(int $projectId): bool {
		return isset(self::$projects[$projectId]);
	}

	/**
	 * @param int $projectId
	 *
	 * @return Project
	 */
	public static function getProject(int $projectId): Project {
		return self::projectExists($projectId) ? self::$projects[$projectId] : null;
	}
}