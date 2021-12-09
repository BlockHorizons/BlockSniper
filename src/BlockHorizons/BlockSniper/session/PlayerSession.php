<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\session;

use BlockHorizons\BlockSniper\brush\Brush;
use BlockHorizons\BlockSniper\changelog\Changelog;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\session\owner\PlayerSessionOwner;
use JsonSerializable;
use pocketmine\block\Air;
use pocketmine\math\VoxelRayTrace;
use pocketmine\world\Position;
use pocketmine\world\World;
use Sandertv\Marshal\DecodeException;
use Sandertv\Marshal\Unmarshal;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function json_encode;
use function strtolower;

/**
 * @phpstan-extends Session<PlayerSessionOwner>
 */
class PlayerSession extends Session implements JsonSerializable{

	private const KEY_LAST_USED_VERSION = "lastUsedVersion";

	public function __construct(PlayerSessionOwner $sessionOwner, Loader $loader){
		$this->dataFile = $loader->getDataFolder() . "sessions/" . strtolower($sessionOwner->getName()) . ".json";
		parent::__construct($sessionOwner, $loader);
	}

	/**
	 * @return bool true if the brush could be recovered from a file.
	 */
	public function initializeBrush() : bool{
		$this->brush = new Brush();
		if(!$this->loader->config->saveBrushProperties){
			return false;
		}
		if(!file_exists($this->dataFile)){
			file_put_contents($this->dataFile, "{}");

			return false;
		}

		$data = file_get_contents($this->dataFile);
		$json = json_decode($data, true);
		if(!isset($json[self::KEY_LAST_USED_VERSION]) || $json[self::KEY_LAST_USED_VERSION] !== Loader::VERSION){
			// We send the changelog to the player only if it used BlockSniper before and if the version the player last
			// used was not equal to the current version of BlockSniper.
			$this->sendChangelog();
		}
		try{
			Unmarshal::json($data, $this->brush);
			$this->loader->getLogger()->debug("Brush recovered:" . $data);

			return true;
		}catch(DecodeException $exception){
			$this->loader->getLogger()->logException($exception);
		}

		return false;
	}

	/**
	 * @return Position
	 */
	public function getTargetBlock() : Position{
		$player = $this->getSessionOwner()->getPlayer();
		$start = $player->getPosition()->add(0, $player->getEyeHeight(), 0);
		$end = $start->addVector($player->getDirectionVector()->multiply($player->getViewDistance() * 16));
		$world = $player->getWorld();
		$lastVec3 = $player->getPosition();
		foreach(VoxelRayTrace::betweenPoints($start, $end) as $vector3){
			if($vector3->y >= World::Y_MAX or $vector3->y <= 0){
				return Position::fromObject($lastVec3, $world);
			}
			if(!$world->isChunkLoaded($vector3->x >> 4, $vector3->z >> 4)){
				return Position::fromObject($lastVec3, $world);
			}
			if(!($world->getBlockAt($vector3->x, $vector3->y, $vector3->z) instanceof Air)){
				return Position::fromObject($vector3, $world);
			}
			$lastVec3 = $vector3;
		}

		return Position::fromObject($end, $world);
	}

	public function close() : void{
		if(!$this->loader->config->saveBrushProperties){
			return;
		}
		$data = json_encode($this);
		$this->loader->getLogger()->debug("Saved brush:" . $data);
		file_put_contents($this->dataFile, $data);
	}

	/**
	 * @return mixed[]
	 */
	public function jsonSerialize() : array{
		$data = $this->brush->jsonSerialize();
		$data[self::KEY_LAST_USED_VERSION] = Loader::VERSION;

		return $data;
	}

	private function sendChangelog() : void{
		$this->getSessionOwner()->getPlayer()->sendForm(Changelog::$changeLogs[Loader::VERSION]->toForm());
	}
}