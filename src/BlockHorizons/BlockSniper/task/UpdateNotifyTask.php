<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\task;

use BlockHorizons\BlockSniper\Loader;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\Internet;
use function json_decode;
use function sprintf;
use function version_compare;

class UpdateNotifyTask extends AsyncTask{

	private const RELEASES_URL = "https://poggit.pmmp.io/releases.json?name=BlockSniper";

	public function onRun() : void{
		$result = Internet::getURL(self::RELEASES_URL, 10, [], $err);
		$highestVersion = Loader::VERSION;
		$artifactUrl = "";
		$api = "";
		if($result !== null && $result->getCode() === 200){
			$releases = json_decode($result->getBody(), true);
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
		$artifactUrl = $artifactUrl . "/BlockSniper_v" . $highestVersion . ".phar";
		$loader->getLogger()->info(sprintf("Version %s has been released for API %s. Download the new release at %s",
				$highestVersion, $api, $artifactUrl
			)
		);
	}
}