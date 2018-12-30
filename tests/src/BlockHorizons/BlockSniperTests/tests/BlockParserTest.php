<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniperTests\tests;

use BlockHorizons\BlockSniper\parser\BlockParser;
use BlockHorizons\BlockSniperTests\Test;

class BlockParserTest extends Test{

	public function onRun() : bool{
		$str = "Stone[facing=north,colour=blue],Grass[width=3]";
		$blocks = BlockParser::parse($str);
		if(count($blocks) !== 2){
			return false;
		}

		$str = "sand[a=b]";
		if(count(BlockParser::parse($str)) !== 1){
			return false;
		}

		try {
			$str = "gravel[facing=up,colour=";
			BlockParser::parse($str);
		}catch(\Throwable $exception){
			return true;
		}

		return false;
	}
}