<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\changelog;

use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\ui\form\ModalForm;
use BlockHorizons\BlockSniper\ui\window\ChangeLogMenu;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class Changelog{

	/** @var Changelog[] */
	public static $changeLogs = [];

	/** @var string */
	private $version;
	/** @var string */
	private $date;

	/** @var string[] */
	private $added, $changed, $removed, $fixed;

	/**
	 * @param string[] $added
	 * @param string[] $changed
	 * @param string[] $removed
	 * @param string[] $fixed
	 */
	public function __construct(string $version, string $date, array $added, array $changed, array $removed, array $fixed){
		$this->date = $date;
		$this->version = $version;
		$this->added = $this->filter($added);
		$this->changed = $this->filter($changed);
		$this->removed = $this->filter($removed);
		$this->fixed = $this->filter($fixed);
	}

	/**
	 * @return ModalForm
	 */
	public function toForm() : ModalForm{
		$text = "";
		foreach(["added" => $this->added, "changed" => $this->changed, "removed" => $this->removed, "fixed" => $this->fixed] as $key => $changes){
			switch($key){
				case "added":
					$text .= TextFormat::GREEN;
					break;
				case "changed":
					$text .= TextFormat::GOLD;
					break;
				case "removed":
					$text .= TextFormat::RED;
					break;
				case "fixed":
					$text .= TextFormat::AQUA;
					break;
			}
			$text .= TextFormat::BOLD . ucfirst($key) . TextFormat::RESET . "\n";

			foreach($changes as $change){
				if($change[0] === "-"){
					// Wordwrap it with indentation so that everything is indented properly.
					$text .= wordwrap($change, 46, "\n  ");
				}else{
					// Wordwrap it with even more indentation as this change is indented 4 spaces (or a tab) further.
					$text .= wordwrap(str_replace("-", "o", $change), 42, "\n       ");
				}
				$text .= "\n";
			}
			$text .= "\n";
		}
		$form = new ModalForm("BlockSniper $this->version ($this->date)", $text);
		$form->setYes(function(Player $player){
		}, Translation::get(Translation::UI_CHANGELOG_CLOSE)
		);
		$form->setNo(function(Player $player){
			$player->sendForm(new ChangeLogMenu());
		}, Translation::get(Translation::UI_CHANGELOG_SEE_OTHER)
		);

		return $form;
	}

	/**
	 * @return string
	 */
	public function getVersion() : string{
		return $this->version;
	}

	/**
	 * @return string
	 */
	public function getReleaseDate() : string{
		return $this->date;
	}

	/**
	 * @param string[] $changes
	 *
	 * @return string[]
	 */
	private function filter(array $changes) : array{
		$new = [];
		foreach($changes as $key => $value){
			if(trim($value) !== ""){
				$new[$key] = $value;
			}
		}

		return $new;
	}
}