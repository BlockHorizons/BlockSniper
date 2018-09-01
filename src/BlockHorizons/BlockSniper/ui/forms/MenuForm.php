<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\ui\forms;

use pocketmine\form\Form as FormInterface;
use pocketmine\Player;

class MenuForm extends Form implements FormInterface{

	/** @var callable[] */
	private $buttons = [];

	public function __construct(string $title, string $content){
		$this->data = [
			"type" => "form",
			"title" => $title,
			"content" => $content,
			"buttons" => []
		];
	}

	// callable: function(Player $player)
	public function addOption(string $text, string $iconPath = "", string $iconType = "url", callable $c = null) : void{
		$this->data["buttons"][] = [
			"text" => $text,
			"image" => [
				"type" => $iconType,
				"data" => $iconPath,
			]
		];
		$this->buttons[] = $c ?? function(){
			};
	}

	public function handleResponse(Player $player, $data) : void{
		if($data !== null){
			$this->buttons[(int) $data]($player, (int) $data);
		}

		if($this->responseForm !== null){
			$player->sendForm($this->responseForm);
		}
	}
}