<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\ui\form;

use pocketmine\form\Form as FormInterface;
use pocketmine\player\Player;

/**
 * @phpstan-type SubmitCallback callable(Player, int) : void
 */
class MenuForm extends Form implements FormInterface{

	/**
	 * @var callable[]
	 * @phpstan-var array<int, SubmitCallback>
	 */
	private $buttons = [];

	public function __construct(string $title, string $content){
		$this->data = [
			"type" => "form",
			"title" => $title,
			"content" => $content,
			"buttons" => []
		];
	}

	/**
	 * @phpstan-param SubmitCallback|null $c
	 */
	public function addOption(string $text, string $iconPath = "", string $iconType = "url", callable $c = null) : void{
		$d = ["text" => $text];
		if($iconPath !== ""){
			$d["image"] = [
				"type" => $iconType,
				"data" => $iconPath,
			];
		}
		$this->data["buttons"][] = $d;
		$this->buttons[] = $c ?? function(){
			};
	}

	public function handleResponse(Player $player, $data) : void{
		if($data !== null){
			$this->buttons[(int) $data]($player, (int) $data);
		}
		$this->onSubmit($player);

		if($this->responseForm !== null){
			$player->sendForm($this->responseForm);
		}
	}
}