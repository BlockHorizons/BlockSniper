<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\tasks;

use BlockHorizons\BlockSniper\Loader;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\Internet;

class UpdateNotifyTask extends AsyncTask{

	private const RELEASES_URL = "https://poggit.pmmp.io/releases.json?name=BlockSniper";

	public function onRun() : void{
		$json = Internet::getURL(self::RELEASES_URL, 10, [], $err);
		$highestVersion = Loader::VERSION;
		$artifactUrl = "";
		$api = "";
		if($json !== false){
			$releases = json_decode($json, true);
			foreach($releases as $release){
				if(version_compare($highestVersion, $release["version"], ">=")){
					continue;
				}
				$highestVersion = $release["version"];
				$artifactUrl = $release["artifact_url"];
				$api = $release["api"][0]["from"] . " - " . $release["api"][0]["to"];
			}
		}

		$this->setResult([$highestVersion, $artifactUrl, $api, $err]);
	}

	public function onCompletion() : void{
		/** @var Loader|null $loader */
		$loader = Server::getInstance()->getPluginManager()->getPlugin("BlockSniper");
		if($loader === null){
			return;
		}
		[$highestVersion, $artifactUrl, $api, $err] = $this->getResult();
		if($err !== null){
			$loader->getLogger()->error("Update notify error: " . $err);
		}
		if($highestVersion === Loader::VERSION){
			return;
		}
		$loader->getLogger()->info(vsprintf("Version %s has been released for API %s. Download the new release at %s",
									   [$highestVersion, $api, $artifactUrl]));
	}
}
