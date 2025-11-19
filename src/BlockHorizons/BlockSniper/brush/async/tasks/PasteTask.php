<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\async\tasks;

use BlockHorizons\BlockSniper\brush\async\BlockSniperChunkManager;
use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\revert\AsyncRevert;
use BlockHorizons\BlockSniper\session\SessionManager;
use BlockHorizons\libschematic\Schematic;
use pocketmine\block\VanillaBlocks;
use pocketmine\block\RuntimeBlockStateRegistry;
use pocketmine\math\Vector3;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\world\sound\FizzSound;
use pocketmine\world\format\Chunk;
use pocketmine\world\format\io\FastChunkSerializer;
use pocketmine\world\World;
use function microtime;
use function round;

class PasteTask extends AsyncTask{

	private const KEY_CHUNKS = "chunks";

	/** @var string */
	private $file;
	/** @var int */
	private $centerX;
	/** @var int */
	private $centerY;
	/** @var int */
	private $centerZ;
	/** @var string */
	private $playerName;
	/** @var float */
	private $startTime;

	public function __construct(string $file, Vector3 $center, array $chunks, string $playerName){
		$this->storeLocal(self::KEY_CHUNKS, $chunks);
		$this->file = $file;
		$this->centerX = (int) $center->x;
		$this->centerY = (int) $center->y;
		$this->centerZ = (int) $center->z;
		$this->playerName = $playerName;
		$this->startTime = microtime(true);
	}

	public function onRun(): void {
		$schematic = new Schematic();
		$schematic->parse($this->file);

		$width = $schematic->getWidth();
		$length = $schematic->getLength();
		$baseX = $this->centerX - (int) ($width / 2);
		$baseZ = $this->centerZ - (int) ($length / 2);

		/** @var array<int, array{id:int,x:int,y:int,z:int}> $blocksData */
		$blocksData = [];

		foreach($schematic->blocks() as $block){
			if($block->getIdInfo()->getBlockTypeId() === VanillaBlocks::AIR()->getIdInfo()->getBlockTypeId()){ 
				continue;
			}
			$blocksData[] = [
				"id" => $block->getStateId(),
				"x" => $baseX + $block->getPosition()->x,
				"y" => $this->centerY + $block->getPosition()->y,
				"z" => $baseZ + $block->getPosition()->z
			];
		}

		$this->setResult($blocksData);
	}

	public function onCompletion(): void {
		/** @var Loader $loader */
		$loader = Server::getInstance()->getPluginManager()->getPlugin("BlockSniper");
		if(!$loader->isEnabled()){
			return;
		}
		if(!($player = Server::getInstance()->getPlayerExact($this->playerName))){
			return;
		}

		/** @var array<int, array{id:int,x:int,y:int,z:int}> $blocksData */
		$blocksData = $this->getResult();

		$world = $player->getWorld();
		$manager = new BlockSniperChunkManager($world::Y_MIN, $world::Y_MAX);

		foreach($blocksData as $data){
			$block = RuntimeBlockStateRegistry::getInstance()->fromStateId($data["id"]);
			$world->setBlockAt($data["x"], $data["y"], $data["z"], $block);
		}

		/** @var Chunk[] $chunks */
		$chunks = [];

		foreach ($blocksData as $data) {
			$chunkX = $data["x"] >> 4;
			$chunkZ = $data["z"] >> 4;
			$chunk = $world->getChunk($chunkX, $chunkZ);
			$hash = World::chunkHash($chunkX, $chunkZ);

			if (!isset($chunks[$hash])) {
				$chunks[$hash] = $chunk;
			}
		}

		$undoChunks = $this->fetchLocal(self::KEY_CHUNKS);
		foreach($undoChunks as &$undoChunk){
			$undoChunk = FastChunkSerializer::deserializeTerrain($undoChunk);
		}

		$duration = round(microtime(true) - $this->startTime, 2);
		$player->sendPopup(TextFormat::GREEN . Translation::get(Translation::BRUSH_STATE_DONE) . " ($duration seconds)");
		$world->addSound($player->getPosition(), new FizzSound(), [$player]);

		SessionManager::getPlayerSession($player)->getRevertStore()->saveUndo(new AsyncRevert($chunks, $undoChunks, $world));
	}

}
