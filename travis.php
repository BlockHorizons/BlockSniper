<?php

declare(strict_types=1);

$server = proc_open(PHP_BINARY . " /home/travis/build/PocketMine-MP/PocketMine-MP.phar --no-wizard --disable-readline", [
	0 => ['pipe', 'r'],
	1 => ['pipe', 'w'],
	2 => ['pipe', 'w'],
], $pipes);

fwrite($pipes[0], "blocksniper\nmakeplugin BlockSniper\nstop\n\n");
while(!feof($pipes[1])){
	echo fgets($pipes[1]);
}

fclose($pipes[0]);
fclose($pipes[1]);
fclose($pipes[2]);

echo "\n\nReturn value: " . proc_close($server) . "\n";
if(count(glob('PocketMine-MP/plugins/DevTools/BlockSniper*.phar')) === 0){
	echo "The BlockSniper Travis CI build failed.\n";
	exit(1);
}

echo "The BlockSniper Travis CI build succeeded.\n";
exit(0);
