<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\changelog;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\Internet;

class ChangelogTask extends AsyncTask{

	private const CHANGELOG_URL = "https://raw.githubusercontent.com/BlockHorizons/BlockSniper/API-4.0.0/CHANGELOG.md";

	public function onRun() : void{
		$result = Internet::getURL(self::CHANGELOG_URL, 10, [], $err);
		$changeLogs = [];
		if($result !== null){
			$reader = new StringReader($result->getBody());
			while(true){
				if(!$reader->canReadUntil("## [")){
					// We've processed all versions in the file.
					break;
				}
				$reader->readUntil("## [");
				$version = $reader->readUntil("]");

				$reader->readUntil("- ");
				$date = trim($reader->readUntil("### Added\n"));
				$added = explode("\n", $reader->readUntil("### Changed"));
				$changed = explode("\n", $reader->readUntil("### Removed"));
				$removed = explode("\n", $reader->readUntil("### Fixed"));
				if(!$reader->canReadUntil("\n\n")){
					$fixed = explode("\n", $reader->remaining());
				}else{
					$fixed = explode("\n", $reader->readUntil("\n\n"));
				}

				$changeLogs[$version] = new Changelog($version, $date, $added, $changed, $removed, $fixed);
			}
		}

		$this->setResult([$changeLogs, $err]);
	}

	public function onCompletion() : void{
		$loader = Server::getInstance()->getPluginManager()->getPlugin("BlockSniper");
		if($loader === null){
			return;
		}
		[$changeLogs, $err] = $this->getResult();
		if($err !== null){
			$loader->getLogger()->error("Changelog retrieving error: " . $err);
		}
		Changelog::$changeLogs = $changeLogs;
	}
}