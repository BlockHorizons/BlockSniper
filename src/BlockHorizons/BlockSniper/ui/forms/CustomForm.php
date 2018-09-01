<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\ui\forms;

use pocketmine\form\Form as FormInterface;
use pocketmine\Player;

class CustomForm extends Form implements FormInterface{

	/** @var callable[] */
	private $elements = [];

	public function __construct(string $title){
		$this->data = [
			"type" => "custom_form",
			"title" => $title,
			"content" => []
		];
	}

	// Callables in the form function(Player $player, $value)

	public function addDropdown(string $text, array $options, int $defaultIndex, callable $c = null) : void{
		$this->data["content"][] = [
			"type" => "dropdown",
			"text" => $text,
			"options" => $options,
			"default" => $defaultIndex,
		];
		$this->elements[] = $c ?? function(){
			};
	}

	public function addInput(string $text, string $default, string $placeHolder, callable $c = null) : void{
		$this->data["content"][] = [
			"type" => "input",
			"text" => $text,
			"default" => $default,
			"placeholder" => $placeHolder
		];
		$this->elements[] = $c ?? function(){
			};
	}

	public function addLabel(string $text) : void{
		$this->data["content"][] = [
			"type" => "label",
			"text" => $text
		];
		$this->elements[] = $c ?? function(){
			};
	}

	public function addSlider(string $text, float $min, float $max, float $stepSize, float $default, callable $c = null) : void{
		$this->data["content"][] = [
			"type" => "slider",
			"text" => $text,
			"min" => $min,
			"max" => $max,
			"step" => $stepSize,
			"default" => $default
		];
		$this->elements[] = $c ?? function(){
			};
	}

	public function addStepSlider(string $text, array $steps, int $defaultIndex, callable $c = null) : void{
		$this->data["content"][] = [
			"type" => "step_slider",
			"text" => $text,
			"steps" => $steps,
			"default" => $defaultIndex
		];
		$this->elements[] = $c ?? function(){
			};
	}

	public function addToggle(string $text, bool $default, callable $c = null) : void{
		$this->data["content"][] = [
			"type" => "toggle",
			"text" => $text,
			"default" => $default
		];
		$this->elements[] = $c ?? function(){
			};
	}

	public function handleResponse(Player $player, $data) : void{
		foreach((array) $data as $index => $value){
			$this->elements[$index]($player, $value);
		}

		if($this->responseForm !== null){
			$player->sendForm($this->responseForm);
		}
	}
}