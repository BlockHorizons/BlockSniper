<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniperTests\tests;

use BlockHorizons\BlockSniper\parser\Parser;
use BlockHorizons\BlockSniperTests\Test;

class BlockParserTest extends Test{

	public function onRun() : bool{
		$str = '
		Stone [
			facing = north,
			colour = blue
		], 
		Grass [
			width = 3
		]
		';
		$blocks = Parser::parse($str);
		if(count($blocks) !== 2){
			return false;
		}

		$str = "sand[a=b]";
		if(count(Parser::parse($str)) !== 1){
			return false;
		}

		try {
			$str = "gravel[facing=up,colour=";
			Parser::parse($str);
		}catch(\Throwable $exception){
			return true;
		}

		return false;
	}
}