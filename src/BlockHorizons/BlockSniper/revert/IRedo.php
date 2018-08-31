<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\revert;

interface IRedo{

	public function getDetached() : Revert;

	public function restore() : void;
}